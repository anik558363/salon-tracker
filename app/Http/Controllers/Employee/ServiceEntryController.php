<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceEntryController extends Controller
{
    public function create()
    {
        $services = Service::where('status', true)->get();
        return view('employee.entries.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'services'       => 'required|array|min:1',
            'services.*'     => 'exists:services,id',
            'note'           => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $customerId = null;

            // ফোন নম্বর থাকলে ফোন দিয়ে চেক/তৈরি
            if ($request->filled('customer_phone')) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    ['name'  => $request->customer_name ?? 'Walk-in Client']
                );
                $customerId = $customer->id;
            } elseif ($request->filled('customer_name')) {
                // শুধু নাম থাকলে নতুন ক্লায়েন্ট তৈরি
                $customer = Customer::create([
                    'name'  => $request->customer_name,
                    'phone' => null,
                ]);
                $customerId = $customer->id;
            }

            // সার্ভিসের বর্তমান রেট রিড করা
            $selectedServices = Service::whereIn('id', $request->services)->get();
            $totalAmount = $selectedServices->sum('price');

            // মূল ইনভয়েস/এন্ট্রি তৈরি (মোট বিল এখানে লক থাকবে)
            $entry = ServiceEntry::create([
                'employee_id'  => auth()->id(),
                'customer_id'  => $customerId,
                'entry_date'   => today(),
                'total_amount' => $totalAmount,
                'note'         => $request->note,
            ]);

            // প্রতিটি আইটেমে তখনকার রেটের স্ন্যাপশট সেভ করা
            foreach ($selectedServices as $service) {
                $entry->items()->create([
                    'service_id' => $service->id,
                    'price'      => $service->price,
                ]);
            }
        });

        return redirect()->route('employee.entries.create')->with('success', 'Service entry saved successfully!');
    }
}
