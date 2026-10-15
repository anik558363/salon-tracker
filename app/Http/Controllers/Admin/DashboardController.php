<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ১. ওভারভিউ মেট্রিকস
        $todayIncome = ServiceEntry::whereDate('entry_date', today())->sum('total_amount');
        $thisMonthIncome = ServiceEntry::whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year)
            ->sum('total_amount');

        $todayServicesCount = DB::table('service_entry_items')
            ->join('service_entries', 'service_entry_items.service_entry_id', '=', 'service_entries.id')
            ->whereDate('service_entries.entry_date', today())
            ->count();

        $totalEmployees = User::role('employee')->where('status', true)->count();

        // ২. সবচেয়ে বেশি বিক্রি হওয়া সার্ভিসসমূহ (Top Sold Services for Chart)
        $topServices = DB::table('service_entry_items')
            ->join('services', 'service_entry_items.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('COUNT(service_entry_items.id) as total_sold'))
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('total_sold')
            ->take(6)
            ->get();

        $chartLabels = $topServices->pluck('name')->toArray();
        $chartSeries = $topServices->pluck('total_sold')->toArray();

        // ৩. স্টাফ পারফর্ম্যান্স
        $employeeStats = User::role('employee')
            ->select('id', 'name')
            ->withCount([
                'serviceEntries as today_entries_count' => function ($query) {
                    $query->whereDate('entry_date', today());
                }
            ])
            ->withSum([
                'serviceEntries as today_income' => function ($query) {
                    $query->whereDate('entry_date', today());
                }
            ], 'total_amount')
            ->withSum([
                'serviceEntries as total_income' => function ($query) {
                    $query->whereMonth('entry_date', now()->month);
                }
            ], 'total_amount')
            ->get();

        // ৪. সাম্প্রতিক এন্ট্রি
        $recentEntries = ServiceEntry::with(['employee', 'customer', 'items.service'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'todayIncome',
            'thisMonthIncome',
            'todayServicesCount',
            'totalEmployees',
            'chartLabels',
            'chartSeries',
            'employeeStats',
            'recentEntries'
        ));
    }
}
