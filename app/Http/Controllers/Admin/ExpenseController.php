<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $categories = ExpenseCategory::orderBy('name')->get();

        $expenses = Expense::with(['category', 'creator'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('note', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('expense_category_id', $request->category_id);
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                $q->whereDate('expense_date', $request->date);
            })
            ->latest('expense_date')
            ->paginate(10)
            ->withQueryString();

        $totalExpense = Expense::when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('expense_category_id', $request->category_id);
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                $q->whereDate('expense_date', $request->date);
            })
            ->sum('amount');

        return view('admin.expenses.index', compact('expenses', 'categories', 'totalExpense'));
    }

    public function create()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('admin.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount'              => 'required|numeric|min:0.01',
            'expense_date'        => 'required|date',
            'note'                => 'nullable|string',
        ]);

        Expense::create([
            'name'                => $request->name,
            'expense_category_id' => $request->expense_category_id,
            'amount'              => $request->amount,
            'expense_date'        => $request->expense_date,
            'note'                => $request->note,
            'user_id'             => auth()->id(), // স্বয়ংক্রিয়ভাবে লগইন করা অ্যাডমিনের আইডি সেভ হবে
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense recorded successfully!');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('admin.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount'              => 'required|numeric|min:0.01',
            'expense_date'        => 'required|date',
            'note'                => 'nullable|string',
        ]);

        $expense->update([
            'name'                => $request->name,
            'expense_category_id' => $request->expense_category_id,
            'amount'              => $request->amount,
            'expense_date'        => $request->expense_date,
            'note'                => $request->note,
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('admin.expenses.index')->with('success', 'Expense record deleted successfully!');
    }
}
