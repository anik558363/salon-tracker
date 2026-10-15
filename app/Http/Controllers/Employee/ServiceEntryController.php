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
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            // কাস্টমার হ্যান্ডলিং
            $customerId = null;
            if ($request->filled('customer_phone') || $request->filled('customer_name')) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    ['name' => $request->customer_name]
                );
                $customerId = $customer->id;
            }

            // সিলেক্টেড সার্ভিসগুলোর প্রাইস ক্যালকুলেট
            $selectedServices = Service::whereIn('id', $request->services)->get();
            $totalAmount = $selectedServices->sum('price');

            // প্যারেন্ট এন্ট্রি তৈরি
            $entry = ServiceEntry::create([
                'employee_id' => auth()->id(),
                'customer_id' => $customerId,
                'entry_date' => today(),
                'total_amount' => $totalAmount,
                'note' => $request->note,
            ]);

            // সার্ভিস আইটেমস স্ন্যাপশট প্রাইস সহ সেভ
            foreach ($selectedServices as $service) {
                $entry->items()->create([
                    'service_id' => $service->id,
                    'price' => $service->price,
                ]);
            }
        });

        return redirect()->route('employee.dashboard')->with('success', 'সার্ভিস এন্ট্রি সফলভাবে সম্পন্ন হয়েছে!');
    }
}
