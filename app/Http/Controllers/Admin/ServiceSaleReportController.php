<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceEntryItem;
use Illuminate\Http\Request;

class ServiceSaleReportController extends Controller
{
    public function index(Request $request)
    {
        $allServices = Service::orderBy('name')->get();

        $startDate = $request->start_date;
        $endDate   = $request->end_date;

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

        $query = Service::query();

        // 1. Service Dropdown Filter
        if ($request->filled('service_id')) {
            $query->where('id', $request->service_id);
        }

        // 2. Keyboard Live Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . trim($request->search) . '%');
        }

        $serviceSales = $query->with(['entryItems' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('serviceEntry', function ($entryQuery) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $entryQuery->whereBetween('entry_date', [$startDate, $endDate]);
                    }
                })->with(['serviceEntry.customer', 'serviceEntry.employee'])->latest();
            }])
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

        // Overall Summary Counts with Filter Support
        $overallSoldQuery = ServiceEntryItem::query();
        if ($startDate && $endDate) {
            $overallSoldQuery->whereHas('serviceEntry', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entry_date', [$startDate, $endDate]);
            });
        }
        if ($request->filled('service_id')) {
            $overallSoldQuery->where('service_id', $request->service_id);
        }
        if ($request->filled('search')) {
            $overallSoldQuery->whereHas('service', function ($q) use ($request) {
                $q->where('name', 'like', '%' . trim($request->search) . '%');
            });
        }

        $overallTotalSold    = (clone $overallSoldQuery)->count();
        $overallTotalRevenue = (clone $overallSoldQuery)->sum('price');

        if ($request->ajax()) {
            $rendered = view('admin.reports.service-sales', compact(
                'serviceSales',
                'allServices',
                'overallTotalSold',
                'overallTotalRevenue',
                'startDate',
                'endDate'
            ))->renderSections();

            return response()->json([
                'table'               => $rendered['table_content'] ?? '',
                'overallTotalSold'    => number_format($overallTotalSold) . ' times',
                'overallTotalRevenue' => 'AED ' . number_format($overallTotalRevenue, 2),
            ]);
        }

        return view('admin.reports.service-sales', compact(
            'serviceSales',
            'allServices',
            'overallTotalSold',
            'overallTotalRevenue',
            'startDate',
            'endDate'
        ));
    }
}
