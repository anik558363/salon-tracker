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
                $search = trim($request->search);
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->withCount('expenses')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.expenses.categories.index', compact('categories'))->renderSections()['table_content'] ?? '',
            ]);
        }

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
