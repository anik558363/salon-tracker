<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceEntryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceSaleReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        // Date Preset Filters
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $startDate = today()->toDateString();
                    $endDate   = today()->toDateString();
                    break;
                case 'this_week':
                    $startDate = now()->startOfWeek()->toDateString();
                    $endDate   = now()->endOfWeek()->toDateString();
                    break;
                case 'this_month':
                    $startDate = now()->startOfMonth()->toDateString();
                    $endDate   = now()->endOfMonth()->toDateString();
                    break;
                case 'this_year':
                    $startDate = now()->startOfYear()->toDateString();
                    $endDate   = now()->endOfYear()->toDateString();
                    break;
            }
        }

        // 1. Service Sales breakdown with Customer Details
        $serviceSales = Service::query()
            ->with(['entryItems' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('serviceEntry', function ($entryQuery) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $entryQuery->whereBetween('entry_date', [$startDate, $endDate]);
                    }
                })->with(['serviceEntry.customer', 'serviceEntry.employee'])->latest();
            }])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->withCount(['entryItems as total_sold_count' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('serviceEntry', function ($entryQuery) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $entryQuery->whereBetween('entry_date', [$startDate, $endDate]);
                    }
                });
            }])
            ->withSum(['entryItems as total_revenue' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('serviceEntry', function ($entryQuery) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $entryQuery->whereBetween('entry_date', [$startDate, $endDate]);
                    }
                });
            }], 'price')
            ->orderByDesc('total_sold_count')
            ->paginate(10)
            ->withQueryString();

        // 2. Summary Counts
        $overallTotalSold = ServiceEntryItem::whereHas('serviceEntry', function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('entry_date', [$startDate, $endDate]);
            }
        })->count();

        $overallTotalRevenue = ServiceEntryItem::whereHas('serviceEntry', function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('entry_date', [$startDate, $endDate]);
            }
        })->sum('price');

        return view('admin.reports.service-sales', compact(
            'serviceSales',
            'overallTotalSold',
            'overallTotalRevenue',
            'startDate',
            'endDate'
        ));
    }
}
