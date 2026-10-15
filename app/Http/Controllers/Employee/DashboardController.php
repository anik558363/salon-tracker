<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ServiceEntry;

class DashboardController extends Controller
{
    public function index()
    {
        $todayEntries = ServiceEntry::where('employee_id', auth()->id())
            ->whereDate('entry_date', today())
            ->with(['customer', 'items.service'])
            ->latest()
            ->get();

        $todayTotalAmount = $todayEntries->sum('total_amount');
        $todayServiceCount = $todayEntries->sum(fn($entry) => $entry->items->count());

        return view('employee.dashboard', compact('todayEntries', 'todayTotalAmount', 'todayServiceCount'));
    }
}
