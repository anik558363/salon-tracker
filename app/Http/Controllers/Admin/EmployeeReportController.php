<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceEntry;
use App\Models\EmployeeSalary;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeReportController extends Controller
{
    public function index(Request $request)
    {
        $allEmployees = User::role('employee')->orderBy('name')->get();
        $services     = Service::orderBy('name')->get();

        $employeeQuery = User::role('employee');

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
        }, 'salaryPayments' => function ($q) {
            $q->latest('payment_date')->latest('id');
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
        }], 'total_amount')
        ->withSum('salaryPayments as total_paid', 'amount');

        $employees = $employeeQuery->paginate(15)->withQueryString();

        // Overall summary metrics calculation
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

        $totalEntries     = $overallEntriesQuery->count();
        $totalRevenue     = $overallEntriesQuery->sum('total_amount');
        $totalSalary40    = $totalRevenue * 0.40;

        // Filter employee onushare total paid hishab
        $paidQuery = EmployeeSalary::query();
        if ($request->filled('employee_id')) {
            $paidQuery->where('employee_id', $request->employee_id);
        }
        $totalPaidOverall = $paidQuery->sum('amount');
        $totalDueOverall  = max(0, $totalSalary40 - $totalPaidOverall);

        if ($request->ajax()) {
            return response()->json([
                'table'        => view('admin.reports.employee_report', compact(
                    'employees', 'allEmployees', 'services', 'totalEntries',
                    'totalRevenue', 'totalSalary40', 'totalPaidOverall', 'totalDueOverall'
                ))->renderSections()['table_content'],
                'totalEntries' => $totalEntries,
                'totalRevenue' => 'AED ' . number_format($totalRevenue, 2),
                'totalSalary'  => 'AED ' . number_format($totalDueOverall, 2),
                'totalPaid'    => 'AED ' . number_format($totalPaidOverall, 2),
            ]);
        }

        return view('admin.reports.employee_report', compact(
            'employees',
            'allEmployees',
            'services',
            'totalEntries',
            'totalRevenue',
            'totalSalary40',
            'totalPaidOverall',
            'totalDueOverall'
        ));
    }

   public function paySalary(Request $request)
{
    $request->validate([
        'employee_id'    => 'required|exists:users,id',
        'amount'         => 'required|numeric|min:0.01',
        'payment_date'   => 'required|date',
        'payment_method' => 'required|string',
        'note'           => 'nullable|string|max:255',
    ]);

    // ১. এমপ্লয়ির মোট সার্ভিস রেভিনিউ থেকে ৪০% হিসাব
    $employee = User::withSum('serviceEntries as total_revenue', 'total_amount')
                    ->withSum('salaryPayments as total_paid', 'amount')
                    ->findOrFail($request->employee_id);

    $totalRevenue = $employee->total_revenue ?? 0;
    $totalSalary40 = $totalRevenue * 0.40;
    $totalPaid = $employee->total_paid ?? 0;

    // বর্তমান ডিউ ব্যালেন্স
    $currentDue = max(0, $totalSalary40 - $totalPaid);

    // ২. শর্ত ১: কোনো ডিউ ব্যালেন্স না থাকলে পেমেন্ট নেওয়া যাবে না
    if ($currentDue <= 0) {
        return redirect()->back()->with('error', 'এই কর্মচারীর কোনো বকেয়া (Due Balance) নেই, তাই পে করা যাবে না!');
    }

    // ৩. শর্ত ২: পে করার পরিমাণ ব্যালেন্সের চেয়ে বেশি হতে পারবে না
    if ($request->amount > $currentDue) {
        return redirect()->back()->with('error', 'পে করার পরিমাণ বর্তমান বকেয়া (AED ' . number_format($currentDue, 2) . ') এর চেয়ে বেশি হতে পারবে না!');
    }

    // সব ঠিক থাকলে পেমেন্ট সেভ হবে
    EmployeeSalary::create([
        'employee_id'    => $request->employee_id,
        'paid_by'        => auth()->id(),
        'amount'         => $request->amount,
        'payment_date'   => $request->payment_date,
        'payment_method' => $request->payment_method,
        'note'           => $request->note,
    ]);

    return redirect()->back()->with('success', 'Salary payout recorded successfully!');
}

}
