@extends('layouts.adminlte')

@section('title', 'Master Financial Ledger')
@section('page_title', 'Master Financial Ledger & Audit Trail')

@section('content')
    <!-- Financial Summary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="p-3 premium-card d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background: rgba(16, 185, 129, 0.1); color: #34d399;">
                    <i class="bi bi-cash-stack fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Total Revenue (Cash In)</span>
                    <h4 class="mb-0 fw-bold text-success font-monospace" id="metricTotalCredits">AED
                        {{ number_format($totalCredits, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="p-3 premium-card d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background: rgba(239, 68, 68, 0.1); color: #f87171;">
                    <i class="bi bi-wallet2 fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Total Debits (Cash Out)</span>
                    <h4 class="mb-0 fw-bold text-danger font-monospace" id="metricTotalDebits">AED
                        {{ number_format($totalDebits, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="p-3 premium-card d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background: rgba(245, 158, 11, 0.1); color: #fbbf24;">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Staff Payouts</span>
                    <h4 class="mb-0 fw-bold text-warning font-monospace" id="metricTotalSalaries">AED
                        {{ number_format($totalSalaries, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="p-3 premium-card d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
                    <i class="bi bi-bank fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Net Cash Balance</span>
                    <h4 class="mb-0 fw-bold {{ $netCashBalance >= 0 ? 'text-primary' : 'text-danger' }} font-monospace"
                        id="metricNetBalance">
                        AED {{ number_format($netCashBalance, 2) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="premium-card mb-4">
                <!-- Filter Bar -->
                <div class="p-4 border-bottom border-secondary border-opacity-10">
                    <form id="ledgerFilterForm" onsubmit="return false;">
                        <div class="row g-3 align-items-end">

                            <!-- ১. লাইভ সার্চ ইনপুট (কী-বোর্ড টাইপিং) -->
                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-semibold">Search (Client, Stylist, Invoice,
                                    Note)</label>
                                <div class="input-group">

                                    <input type="text" id="liveSearchInput" name="search"
                                        class="form-control filter-input" placeholder="Type keyword..."
                                        value="{{ request('search') }}">
                                </div>
                            </div>

                            <!-- ২. এমপ্লয়ি ড্রপডাউন ফিল্টার -->
                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-semibold">Stylist / Employee</label>
                                <select name="employee_id" class="form-select filter-input auto-trigger">
                                    <option value="">All Stylists</option>
                                    @foreach ($allEmployees as $emp)
                                        <option value="{{ $emp->id }}"
                                            {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- ৩. সার্ভিস ড্রপডাউন ফিল্টার -->
                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-semibold">Service Rendered</label>
                                <select name="service_id" class="form-select filter-input auto-trigger">
                                    <option value="">All Services</option>
                                    @foreach ($allServices as $srv)
                                        <option value="{{ $srv->id }}"
                                            {{ request('service_id') == $srv->id ? 'selected' : '' }}>
                                            {{ $srv->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- ৪. ট্রানজেকশন টাইপ -->
                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-semibold">Transaction Flow</label>
                                <select name="type" class="form-select filter-input auto-trigger">
                                    <option value="">All Flows</option>
                                    <option value="credit">Cash In (Revenue)</option>
                                    <option value="debit_salary">Staff Payouts</option>
                                    <option value="debit_expense">Salon Expenses</option>
                                </select>
                            </div>

                            <!-- ৫. তারিখ রেঞ্জ -->
                            <div class="col-md-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label text-muted small fw-semibold">From</label>
                                        <input type="date" name="from_date"
                                            class="form-control filter-input auto-trigger"
                                            value="{{ request('from_date') }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-muted small fw-semibold">To</label>
                                        <input type="date" name="to_date" class="form-control filter-input auto-trigger"
                                            value="{{ request('to_date') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-auto d-flex align-items-center justify-content-end gap-2 mt-2 ">

                                <div id="loadingSpinner" class="spinner-border spinner-border-sm text-warning d-none"
                                    role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <button type="button" id="resetFiltersBtn"
                                    class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Header -->
                <div class="premium-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-white"><i class="bi bi-journal-text text-warning me-2"></i>Unified
                            Financial Ledger</h5>
                        <small class="text-muted">Dynamic search and multi-filtering enabled</small>
                    </div>
                </div>

                <!-- Table Container for AJAX -->
                <div class="table-responsive" id="ledgerTableContainer">
                    @include('admin.reports.partials._ledger_table')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('ledgerFilterForm');
            const searchInput = document.getElementById('liveSearchInput');
            const autoTriggers = document.querySelectorAll('.auto-trigger');
            const resetBtn = document.getElementById('resetFiltersBtn');
            const tableContainer = document.getElementById('ledgerTableContainer');
            const loadingSpinner = document.getElementById('loadingSpinner');

            const metricCredits = document.getElementById('metricTotalCredits');
            const metricDebits = document.getElementById('metricTotalDebits');
            const metricSalaries = document.getElementById('metricTotalSalaries');
            const metricBalance = document.getElementById('metricNetBalance');

            let debounceTimer;

            function fetchLedger(url = "{{ route('admin.reports.financial-ledger') }}") {
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
                        metricCredits.textContent = data.totalCredits;
                        metricDebits.textContent = data.totalDebits;
                        metricSalaries.textContent = data.totalSalaries;
                        metricBalance.textContent = data.netCashBalance;
                    })
                    .catch(err => console.error("Error:", err))
                    .finally(() => {
                        loadingSpinner.classList.add('d-none');
                    });
            }

            // কী-বোর্ডে টাইপ করার সাথে সাথে রিয়েল-টাইম সার্চ (Debounced 350ms)
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchLedger();
                }, 350);
            });

            // ড্রপডাউন সিলেক্ট করলেই সাথে সাথে রিলোড ছাড়া ডাটা লোড
            autoTriggers.forEach(input => {
                input.addEventListener('change', () => fetchLedger());
            });

            // ফিল্টার রিসেট
            resetBtn.addEventListener('click', function() {
                form.reset();
                fetchLedger();
            });
        });
    </script>
@endpush
