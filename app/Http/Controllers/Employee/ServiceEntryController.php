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
            // Customer handling
            $customerId = null;
            if ($request->filled('customer_phone') || $request->filled('customer_name')) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    ['name'  => $request->customer_name]
                );
                $customerId = $customer->id;
            }

            // Selected services price calculation
            $selectedServices = Service::whereIn('id', $request->services)->get();
            $totalAmount = $selectedServices->sum('price');

            // Parent service entry toiri
            $entry = ServiceEntry::create([
                'employee_id'  => auth()->id(),
                'customer_id'  => $customerId,
                'entry_date'   => today(),
                'total_amount' => $totalAmount,
                'note'         => $request->note,
            ]);

            // Item-wise snapshot price save kora
            foreach ($selectedServices as $service) {
                $entry->items()->create([
                    'service_id' => $service->id,
                    'price'      => $service->price,
                ]);
            }
        });

        // Dashboard route na thakay abar notun service entry page-e redirect kora holo
        return redirect()->route('employee.entries.create')->with('success', 'Service entry saved successfully!');
    }
}
