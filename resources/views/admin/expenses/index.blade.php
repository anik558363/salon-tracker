@extends('layouts.adminlte')

@section('title', 'Expenses Management')
@section('page_title', 'Salon Expenses & Costs')

@section('content')
<!-- Stats Bar -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(239, 68, 68, 0.1); color: #f87171;">
                <i class="bi bi-wallet2 fs-3"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Expenses (Filtered)</span>
                <h4 class="mb-0 fw-bold text-danger">AED {{ number_format($totalExpense, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success bg-opacity-10 border-success border-opacity-25 text-success alert-dismissible fade show mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="premium-card mb-4">
            <!-- Filter Section -->
            <div class="p-4 border-bottom border-secondary border-opacity-10">
                <form action="{{ route('admin.expenses.index') }}" method="GET">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-4 col-md-6">
                            <input type="text" name="search" class="form-control filter-input" placeholder="Search title or note..." value="{{ request('search') }}">
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <select name="category_id" class="form-select filter-input">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <input type="date" name="date" class="form-control filter-input" value="{{ request('date') }}">
                        </div>

                        <div class="col-lg-2 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'category_id', 'date']))
                                <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3" title="Clear Filters">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Header & Buttons -->
            <div class="premium-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-receipt-cutoff text-danger me-2"></i>Expenses Directory</h5>
                    <small class="text-muted">Track operating overheads, utilities, and salon expenses</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                        <i class="bi bi-tags me-1"></i> Categories
                    </a>
                    <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Add Expense
                    </a>
                </div>
            </div>

            <!-- Expenses Table -->
            <div class="table-responsive">
                <table class="table premium-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Expense Title (Name)</th>
                            <th>Category</th>
                            <th class="text-center">Amount</th>
                            <th>Date</th>
                            <th>Created By</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration + ($expenses->currentPage() - 1) * $expenses->perPage() }}</td>
                            <td>
                                <div class="fw-bold text-light fs-6">{{ $expense->name }}</div>
                                @if($expense->note)
                                    <div class="text-muted small text-truncate" style="max-width: 250px;">
                                        {{ $expense->note }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                                    {{ $expense->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="text-center font-monospace fw-bold text-danger">
                                AED {{ number_format($expense->amount, 2) }}
                            </td>
                            <td class="text-muted small font-monospace">
                                {{ $expense->expense_date->format('d M, Y') }}
                            </td>
                            <td>
                                <span class="text-light small">
                                    <i class="bi bi-person-fill text-secondary me-1"></i>{{ $expense->creator->name ?? 'System' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="btn btn-sm btn-outline-info rounded-3 me-2" title="Edit Expense">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Delete Expense">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary"></i> No expense records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($expenses->hasPages())
                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end">
                    {{ $expenses->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
