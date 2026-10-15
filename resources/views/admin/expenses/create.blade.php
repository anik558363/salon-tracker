@extends('layouts.adminlte')

@section('title', 'Record Expense')
@section('page_title', 'Record New Expense')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="premium-card">
            <div class="premium-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-plus-circle text-primary me-2"></i>Record New Expense</h5>
                <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Expenses
                </a>
            </div>

            <form action="{{ route('admin.expenses.store') }}" method="POST">
                @csrf
                <div class="p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger bg-opacity-10 border-danger border-opacity-25 text-danger alert-dismissible fade show mb-4">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Expense Title / Item Name</label>
                        <input type="text" name="name" class="form-control filter-input" value="{{ old('name') }}" placeholder="e.g. Electricity Bill, Beard Oils Restock" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Category</label>
                            <select name="expense_category_id" class="form-select filter-input" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Amount (AED)</label>
                            <input type="number" step="0.01" name="amount" class="form-control filter-input" value="{{ old('amount') }}" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Expense Date</label>
                        <input type="date" name="expense_date" class="form-control filter-input" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Note / Remarks (Optional)</label>
                        <textarea name="note" rows="3" class="form-control filter-input" placeholder="Any invoice number, supplier details, or remarks...">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
