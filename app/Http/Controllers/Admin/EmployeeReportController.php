<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceEntry;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeReportController extends Controller
{
    public function index(Request $request)
    {
        $allEmployees = User::role('employee')->orderBy('name')->get();
        $services     = Service::orderBy('name')->get();

        // এমপ্লয়ি-ভিত্তিক কুয়েরি
        $employeeQuery = User::role('employee');

        // এমপ্লয়ি ড্রপডাউন বা নাম দিয়ে ফিল্টার
        if ($request->filled('employee_id')) {
            $employeeQuery->where('id', $request->employee_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $employeeQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // রিলেশনশিপ এবং এগ্রিগেশন লোড (তারিখ এবং সার্ভিস ফিল্টারসহ)
        $employeeQuery->with(['serviceEntries' => function ($q) use ($request) {
            if ($request->filled('service_id')) {
                $q->whereHas('items', function ($sq) use ($request) {
                    $sq->where('service_id', $request->service_id);
                });
            }
            if ($request->filled('from_date')) {
                $q->whereDate('entry_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $q->whereDate('entry_date', '<=', $request->to_date);
            }
            $q->with(['customer', 'items.service']);
        }])
            ->withCount(['serviceEntries as total_entries' => function ($q) use ($request) {
                if ($request->filled('service_id')) {
                    $q->whereHas('items', function ($sq) use ($request) {
                        $sq->where('service_id', $request->service_id);
                    });
                }
                if ($request->filled('from_date')) {
                    $q->whereDate('entry_date', '>=', $request->from_date);
                }
                if ($request->filled('to_date')) {
                    $q->whereDate('entry_date', '<=', $request->to_date);
                }
            }])
            ->withSum(['serviceEntries as total_revenue' => function ($q) use ($request) {
                if ($request->filled('service_id')) {
                    $q->whereHas('items', function ($sq) use ($request) {
                        $sq->where('service_id', $request->service_id);
                    });
                }
                if ($request->filled('from_date')) {
                    $q->whereDate('entry_date', '>=', $request->from_date);
                }
                if ($request->filled('to_date')) {
                    $q->whereDate('entry_date', '<=', $request->to_date);
                }
            }], 'total_amount');

        $employees = $employeeQuery->paginate(15)->withQueryString();

        // ওভারঅল সামারি মেট্রিক্স
        $overallEntriesQuery = ServiceEntry::query();
        if ($request->filled('employee_id')) {
            $overallEntriesQuery->where('employee_id', $request->employee_id);
        }
        if ($request->filled('service_id')) {
            $overallEntriesQuery->whereHas('items', function ($sq) use ($request) {
                $sq->where('service_id', $request->service_id);
            });
        }
        if ($request->filled('from_date')) {
            $overallEntriesQuery->whereDate('entry_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $overallEntriesQuery->whereDate('entry_date', '<=', $request->to_date);
        }

        $totalEntries = $overallEntriesQuery->count();
        $totalRevenue = $overallEntriesQuery->sum('total_amount');

        if ($request->ajax()) {
            return response()->json([
                'table'        => view('admin.reports.employee_report', compact('employees', 'allEmployees', 'services', 'totalEntries', 'totalRevenue'))->renderSections()['table_content'],
                'totalEntries' => $totalEntries,
                'totalRevenue' => 'AED ' . number_format($totalRevenue, 2),
            ]);
        }

        return view('admin.reports.employee_report', compact('employees', 'allEmployees', 'services', 'totalEntries', 'totalRevenue'));
    }
}
