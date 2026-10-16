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
                <h4 class="mb-0 fw-bold text-danger font-monospace" id="metricTotalExpense">AED {{ number_format($totalExpense, 2) }}</h4>
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
            <!-- Filter Section (AJAX) -->
            <div class="p-4 border-bottom border-secondary border-opacity-10">
                <form id="expenseFilterForm" onsubmit="return false;">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label text-muted small fw-semibold">Search Title or Note</label>
                            <div class="input-group">
                               
                                <input type="text" id="liveSearchInput" name="search" class="form-control filter-input" placeholder="Type title or note..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <label class="form-label text-muted small fw-semibold">Category</label>
                            <select name="category_id" class="form-select filter-input auto-trigger">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <label class="form-label text-muted small fw-semibold">Expense Date</label>
                            <input type="date" name="date" class="form-control filter-input auto-trigger" value="{{ request('date') }}">
                        </div>

                        <div class="col-lg-2 col-md-12 d-flex justify-content-end gap-2 mt-2">
                            <div id="loadingSpinner" class="spinner-border spinner-border-sm text-warning me-2 d-none" role="status"></div>
                            <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100 py-2 rounded-3 fw-semibold" title="Clear Filters">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset
                            </button>
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

            <!-- Expenses Table Container for AJAX -->
            <div class="table-responsive" id="expenseTableContainer">
                @section('table_content')
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
                            <td class="ps-4 text-muted fw-semibold">
                                {{ $loop->iteration + ($expenses->currentPage() - 1) *$expenses->perPage() }}
                            </td>
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
                                <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary"></i> No expense records found matching criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($expenses->hasPages())
                    <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end" id="ajaxPaginationWrapper">
                        {{ $expenses->links('pagination::bootstrap-5') }}
                    </div>
                @endif
                @show
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('expenseFilterForm');
        const searchInput = document.getElementById('liveSearchInput');
        const autoTriggers = document.querySelectorAll('.auto-trigger');
        const resetBtn = document.getElementById('resetBtn');
        const tableContainer = document.getElementById('expenseTableContainer');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const metricTotal = document.getElementById('metricTotalExpense');

        let debounceTimer;

        function fetchExpenses(url = "{{ route('admin.expenses.index') }}") {
            loadingSpinner.classList.remove('d-none');

            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            const targetUrl = url.includes('?') ? `${url}&${params.toString()}` : `${url}?${params.toString()}`;

            fetch(targetUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                tableContainer.innerHTML = data.table;
                if (metricTotal && data.totalExpense) {
                    metricTotal.textContent = data.totalExpense;
                }
                attachPaginationEvents();
            })
            .catch(err => console.error("AJAX Error:", err))
            .finally(() => {
                loadingSpinner.classList.add('d-none');
            });
        }

        // কী-বোর্ডে টাইপ করার সাথে সাথে লাইভ সার্চ (350ms debounce)
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchExpenses();
            }, 350);
        });

        // ক্যাটাগরি বা ডেট পরিবর্তনের সাথে সাথে অটো লোড
        autoTriggers.forEach(input => {
            input.addEventListener('change', function () {
                fetchExpenses();
            });
        });

        // ফিল্টার রিসেট বাটন
        resetBtn.addEventListener('click', function () {
            form.reset();
            fetchExpenses();
        });

        // পেজিনেশন লিঙ্কগুলোতে ক্লিক করলে পেজ রিলোড ছাড়া লোড
        function attachPaginationEvents() {
            const paginationLinks = tableContainer.querySelectorAll('#ajaxPaginationWrapper a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    if (href) {
                        fetchExpenses(href);
                    }
                });
            });
        }

        attachPaginationEvents();
    });
</script>
@endpush
