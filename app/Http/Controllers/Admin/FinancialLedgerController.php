<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceEntry;
use App\Models\EmployeeSalary;
use App\Models\Expense;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinancialLedgerController extends Controller
{
    public function index(Request $request)
    {
        $allEmployees = User::role('employee')->orderBy('name')->get();
        $allServices  = Service::orderBy('name')->get();

        $fromDate    = $request->from_date;
        $toDate      = $request->to_date;
        $typeFilter  = $request->type;
        $employeeId  = $request->employee_id;
        $serviceId   = $request->service_id;
        $searchTerm  = trim($request->search ?? '');

        // 1. Service Entries (Credit: Cash Inflow)
        $serviceEntriesQuery = ServiceEntry::with(['customer', 'employee', 'items.service']);
        if ($fromDate) {
            $serviceEntriesQuery->whereDate('entry_date', '>=', $fromDate);
        }
        if ($toDate) {
            $serviceEntriesQuery->whereDate('entry_date', '<=', $toDate);
        }
        if ($employeeId) {
            $serviceEntriesQuery->where('employee_id', $employeeId);
        }
        if ($serviceId) {
            $serviceEntriesQuery->whereHas('items', function ($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            });
        }
        if ($searchTerm) {
            $cleanId = ltrim($searchTerm, '#INV-0');
            $serviceEntriesQuery->where(function ($q) use ($searchTerm, $cleanId) {
                $q->whereHas('employee', function ($sq) use ($searchTerm) {
                    $sq->where('name', 'like', "%{$searchTerm}%");
                })->orWhereHas('customer', function ($sq) use ($searchTerm) {
                    $sq->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('phone', 'like', "%{$searchTerm}%");
                })->orWhereHas('items.service', function ($sq) use ($searchTerm) {
                    $sq->where('name', 'like', "%{$searchTerm}%");
                });
                if (!empty($cleanId) && is_numeric($cleanId)) {
                    $q->orWhere('id', $cleanId);
                }
            });
        }

        $serviceEntries = $serviceEntriesQuery->get()->map(function ($entry) {
            $servicesRendered = $entry->items->pluck('service.name')->filter()->unique()->implode(', ');
            return (object) [
                'date'        => Carbon::parse($entry->entry_date),
                'txn_id'      => '#INV-' . str_pad($entry->id, 5, '0', STR_PAD_LEFT),
                'category'    => 'Service Revenue',
                'description' => 'Client: ' . ($entry->customer->name ?? 'Walk-in') . ($servicesRendered ? ' [' . $servicesRendered . ']' : ''),
                'type'        => 'Credit',
                'amount'      => (float) $entry->total_amount,
                'method'      => 'POS Billing',
                'created_by'  => $entry->employee->name ?? 'Staff',
            ];
        });

        // 2. Staff Salary Payouts (Debit: Cash Outflow)
        // সার্ভিস সিলেক্ট থাকলে সেলারি ট্রানজেকশনে দেখানোর দরকার নেই
        $salaries = collect();
        if (!$serviceId) {
            $salariesQuery = EmployeeSalary::with(['employee', 'payer']);
            if ($fromDate) {
                $salariesQuery->whereDate('payment_date', '>=', $fromDate);
            }
            if ($toDate) {
                $salariesQuery->whereDate('payment_date', '<=', $toDate);
            }
            if ($employeeId) {
                $salariesQuery->where('employee_id', $employeeId);
            }
            if ($searchTerm) {
                $salariesQuery->where(function ($q) use ($searchTerm) {
                    $q->whereHas('employee', function ($sq) use ($searchTerm) {
                        $sq->where('name', 'like', "%{$searchTerm}%");
                    })->orWhere('note', 'like', "%{$searchTerm}%")
                      ->orWhere('payment_method', 'like', "%{$searchTerm}%");
                });
            }
            $salaries = $salariesQuery->get()->map(function ($salary) {
                return (object) [
                    'date'        => Carbon::parse($salary->payment_date),
                    'txn_id'      => '#PAY-' . str_pad($salary->id, 5, '0', STR_PAD_LEFT),
                    'category'    => 'Staff Salary Payout',
                    'description' => 'Stylist: ' . ($salary->employee->name ?? 'Deleted') . ($salary->note ? ' (' . $salary->note . ')' : ''),
                    'type'        => 'Debit',
                    'amount'      => (float) $salary->amount,
                    'method'      => $salary->payment_method ?? 'Cash',
                    'created_by'  => $salary->payer->name ?? 'Admin',
                ];
            });
        }

        // 3. Salon Expenses (Debit: Cash Outflow)
        // কোনো নির্দিষ্ট সার্ভিস বা স্পেসিফিক এমপ্লয়ি সিলেক্ট থাকলে সাধারণ সেলুন এক্সপেন্স না আনা ভালো
        $expenses = collect();
        if (!$serviceId && !$employeeId) {
            $expensesQuery = Expense::with(['category', 'user']);
            if ($fromDate) {
                $expensesQuery->whereDate('expense_date', '>=', $fromDate);
            }
            if ($toDate) {
                $expensesQuery->whereDate('expense_date', '<=', $toDate);
            }
            if ($searchTerm) {
                $expensesQuery->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('note', 'like', "%{$searchTerm}%")
                      ->orWhereHas('category', function ($sq) use ($searchTerm) {
                          $sq->where('name', 'like', "%{$searchTerm}%");
                      });
                });
            }
            $expenses = $expensesQuery->get()->map(function ($expense) {
                return (object) [
                    'date'        => Carbon::parse($expense->expense_date),
                    'txn_id'      => '#EXP-' . str_pad($expense->id, 5, '0', STR_PAD_LEFT),
                    'category'    => 'Expense: ' . ($expense->category->name ?? 'General'),
                    'description' => $expense->name . ($expense->note ? ' (' . $expense->note . ')' : ''),
                    'type'        => 'Debit',
                    'amount'      => (float) $expense->amount,
                    'method'      => 'Cash / Bank',
                    'created_by'  => $expense->user->name ?? 'Admin',
                ];
            });
        }

        // Merge & Filter Flow Type
        $allTxns = collect();
        if (!$typeFilter || $typeFilter === 'credit') {
            $allTxns = $allTxns->merge($serviceEntries);
        }
        if (!$typeFilter || $typeFilter === 'debit_salary') {
            $allTxns = $allTxns->merge($salaries);
        }
        if (!$typeFilter || $typeFilter === 'debit_expense') {
            $allTxns = $allTxns->merge($expenses);
        }

        // Sort latest first
        $ledger = $allTxns->sortByDesc(function ($item) {
            return $item->date->timestamp;
        });

        // Totals
        $totalCredits   = $serviceEntries->sum('amount');
        $totalSalaries  = $salaries->sum('amount');
        $totalExpenses  = $expenses->sum('amount');
        $totalDebits    = $totalSalaries + $totalExpenses;
        $netCashBalance = $totalCredits - $totalDebits;

        if ($request->ajax()) {
            return response()->json([
                'table'          => view('admin.reports.partials._ledger_table', compact('ledger'))->render(),
                'totalCredits'   => 'AED ' . number_format($totalCredits, 2),
                'totalDebits'    => 'AED ' . number_format($totalDebits, 2),
                'totalSalaries'  => 'AED ' . number_format($totalSalaries, 2),
                'totalExpenses'  => 'AED ' . number_format($totalExpenses, 2),
                'netCashBalance' => 'AED ' . number_format($netCashBalance, 2),
            ]);
        }

        return view('admin.reports.financial_ledger', compact(
            'ledger',
            'allEmployees',
            'allServices',
            'totalCredits',
            'totalDebits',
            'totalSalaries',
            'totalExpenses',
            'netCashBalance'
        ));
    }
}
