<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceEntry;
use App\Models\EmployeeSalary;
use App\Models\Expense;
use Illuminate\Http\Request;

class IncomeReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        // পিরিয়ড ফিল্টার লজিক
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

        // ১. Total Revenue (Sales)
        $revenueQuery = ServiceEntry::query();
        if ($startDate && $endDate) {
            $revenueQuery->whereBetween('entry_date', [$startDate, $endDate]);
        }
        $totalRevenue = $revenueQuery->sum('total_amount');

        // ২. Total Expenses
        $expenseQuery = Expense::query();
        if ($startDate && $endDate) {
            $expenseQuery->whereBetween('expense_date', [$startDate, $endDate]);
        }
        $totalExpenses = $expenseQuery->sum('amount');

        // ৩. Total Staff Payouts (Salaries Paid)
        $payoutQuery = EmployeeSalary::query();
        if ($startDate && $endDate) {
            $payoutQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }
        $totalPayouts = $payoutQuery->sum('amount');

        // ৪. Total Operational Outflow = Total Expenses + Total Payouts
        $totalOutflow = $totalExpenses + $totalPayouts;

        // ৫. Net Income (Profit/Loss) = Total Revenue - (Total Expenses + Total Payouts)
        $netIncome = $totalRevenue - $totalOutflow;

        // Profit Margin Percentage
        $profitMargin = $totalRevenue > 0 ? ($netIncome / $totalRevenue) * 100 : 0;

        // ব্রেকডাউন তালিকা
        $expenses = $expenseQuery->with('category')->latest('expense_date')->take(10)->get();
        $payouts  = $payoutQuery->with('employee')->latest('payment_date')->take(10)->get();

        if ($request->ajax()) {
            return response()->json([
                'totalRevenue'  => 'AED ' . number_format($totalRevenue, 2),
                'totalExpenses' => 'AED ' . number_format($totalExpenses, 2),
                'totalPayouts'  => 'AED ' . number_format($totalPayouts, 2),
                'totalOutflow'  => 'AED ' . number_format($totalOutflow, 2),
                'netIncome'     => 'AED ' . number_format($netIncome, 2),
                'isProfit'      => $netIncome >= 0,
                'profitMargin'  => number_format($profitMargin, 1) . '%',
            ]);
        }

        return view('admin.reports.income_report', compact(
            'totalRevenue',
            'totalExpenses',
            'totalPayouts',
            'totalOutflow',
            'netIncome',
            'profitMargin',
            'expenses',
            'payouts',
            'startDate',
            'endDate'
        ));
    }
}
