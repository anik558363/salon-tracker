<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = ExpenseCategory::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->withCount('expenses')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.expenses.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.expenses.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        ExpenseCategory::create([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.expense-categories.index')->with('success', 'Expense Category created successfully!');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('admin.expenses.categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $expenseCategory->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.expense-categories.index')->with('success', 'Expense Category updated successfully!');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();

        return redirect()->route('admin.expense-categories.index')->with('success', 'Expense Category deleted successfully!');
    }
}
