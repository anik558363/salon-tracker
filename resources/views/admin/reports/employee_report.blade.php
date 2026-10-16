@extends('layouts.adminlte')

@section('title', 'Employee Performance & Salary Report')
@section('page_title', 'Staff Service Entries & 40% Salary Management')

@section('content')
    <!-- Metric Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="p-3 premium-card d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Total Entries</span>
                    <h4 class="mb-0 fw-bold text-light font-monospace" id="metricTotalEntries">{{ $totalEntries }}</h4>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="p-3 premium-card d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background: rgba(16, 185, 129, 0.1); color: #34d399;">
                    <i class="bi bi-cash-stack fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Total Revenue</span>
                    <h4 class="mb-0 fw-bold text-success font-monospace" id="metricTotalRevenue">AED
                        {{ number_format($totalRevenue, 2) }}</h4>
                </div>
            </div>
        </div>

        <!-- 3rd Card: Net Due Payable (Taka pay korle eti kome jabe) -->
        <div class="col-sm-6 col-lg-3">
            <div class="p-3 premium-card d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background: rgba(245, 158, 11, 0.1); color: #fbbf24;">
                    <i class="bi bi-hourglass-split fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Net Due Payable</span>
                    <h4 class="mb-0 fw-bold text-warning font-monospace" id="metricTotalSalary">AED
                        {{ number_format($totalDueOverall, 2) }}</h4>
                </div>
            </div>
        </div>

        <!-- 4th Card: Total Paid Out -->
        <div class="col-sm-6 col-lg-3">
            <div class="p-3 premium-card d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background: rgba(239, 68, 68, 0.1); color: #f87171;">
                    <i class="bi bi-wallet2 fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Total Paid Out</span>
                    <h4 class="mb-0 fw-bold text-info font-monospace" id="metricTotalPaid">AED
                        {{ number_format($totalPaidOverall, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div
            class="alert alert-success bg-opacity-10 border-success border-opacity-25 text-success alert-dismissible fade show mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger bg-opacity-10 border-danger border-opacity-25 text-danger alert-dismissible fade show mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
@endif

    <div class="row">
        <div class="col-12">
            <div class="premium-card mb-4">
                <!-- Filter Bar -->
                <div class="p-4 border-bottom border-secondary border-opacity-10">
                    <form id="reportFilterForm" onsubmit="return false;">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label text-muted small fw-semibold">Search by Stylist Name</label>
                                <input type="text" id="liveSearchInput" name="search" class="form-control filter-input"
                                    placeholder="Type stylist name or email..." value="{{ request('search') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-semibold">Select Stylist</label>
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

                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-semibold">Filter by Service</label>
                                <select name="service_id" class="form-select filter-input auto-trigger">
                                    <option value="">All Services</option>
                                    @foreach ($services as $srv)
                                        <option value="{{ $srv->id }}"
                                            {{ request('service_id') == $srv->id ? 'selected' : '' }}>
                                            {{ $srv->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-semibold">From Date</label>
                                <input type="date" name="from_date" class="form-control filter-input auto-trigger"
                                    value="{{ request('from_date') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-semibold">To Date</label>
                                <input type="date" name="to_date" class="form-control filter-input auto-trigger"
                                    value="{{ request('to_date') }}">
                            </div>

                            <div class="col-1 d-flex justify-content-end gap-2 mt-2">
                                <div id="loadingSpinner" class="spinner-border spinner-border-sm text-warning me-2 d-none"
                                    role="status"></div>
                                <button type="button" id="resetFiltersBtn"
                                    class="btn btn-outline-secondary py-2 rounded-3 fw-semibold">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Report Header -->
                <div class="premium-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-white"><i class="bi bi-calculator-fill text-warning me-2"></i>Stylist
                            Performance & Salary Ledger</h5>
                        <small class="text-muted">Calculates 40% salary from service revenues with payment payout
                            logs</small>
                    </div>
                </div>

                <!-- Table Container for AJAX -->
                <div class="table-responsive" id="reportTableContainer">
                @section('table_content')
                    <table class="table premium-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>EMPLOYEE (STYLIST)</th>
                                <th class="text-center">ENTRIES</th>
                                <th class="text-end">TOTAL REVENUE</th>
                                <th class="text-end text-warning">40% SALARY</th>
                                <th class="text-end text-info">TOTAL PAID</th>
                                <th class="text-end text-danger">DUE BALANCE</th>
                                <th class="text-center pe-4">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                @php
                                    $empRevenue = $employee->total_revenue ?? 0;
                                    $empSalary = $empRevenue * 0.4;
                                    $empPaid = $employee->total_paid ?? 0;
                                    $empDue = max(0, $empSalary - $empPaid);
                                @endphp
                                <tr>
                                    <td class="ps-4 text-muted fw-semibold font-monospace">
                                        {{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}
                                    </td>
                                    <td>
                                        <div class="fw-bold text-white fs-6">
                                            <i class="bi bi-person-badge text-warning me-1"></i> {{ $employee->name }}
                                        </div>
                                        <span class="text-muted small font-monospace">{{ $employee->email }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 font-monospace">
                                            {{ $employee->total_entries ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="text-end font-monospace text-light">
                                        AED {{ number_format($empRevenue, 2) }}
                                    </td>
                                    <td class="text-end font-monospace fw-bold text-warning fs-6">
                                        AED {{ number_format($empSalary, 2) }}
                                    </td>
                                    <td class="text-end font-monospace text-info font-semibold">
                                        AED {{ number_format($empPaid, 2) }}
                                    </td>
                                    <td
                                        class="text-end font-monospace fw-bold {{ $empDue > 0 ? 'text-danger' : 'text-success' }}">
                                        AED {{ number_format($empDue, 2) }}
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="btn-group btn-group-sm">
                                            @if ($empDue > 0)
                                                <button class="btn btn-outline-success rounded-3 me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#payModal-{{ $employee->id }}">
                                                    <i class="bi bi-cash me-1"></i> Pay
                                                </button>
                                            @else
                                                <button class="btn btn-outline-secondary rounded-3 me-1 opacity-50"
                                                    disabled title="No Due Balance">
                                                    <i class="bi bi-cash me-1"></i> Pay
                                                </button>
                                            @endif
                                            <button class="btn btn-outline-info rounded-3 me-1" data-bs-toggle="modal"
                                                data-bs-target="#paymentHistoryModal-{{ $employee->id }}">
                                                <i class="bi bi-clock-history"></i> History
                                            </button>
                                            <button class="btn btn-outline-warning rounded-3" data-bs-toggle="modal"
                                                data-bs-target="#employeeDetailsModal-{{ $employee->id }}">
                                                <i class="bi bi-eye"></i> Entries
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary"></i>
                                        No stylists found matching criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($employees->hasPages())
                        <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end"
                            id="ajaxPaginationWrapper">
                            {{ $employees->links('pagination::bootstrap-5') }}
                        </div>
                    @endif

                    <!-- Modals table er baire rakha hoyeche -->
                    @foreach ($employees as $employee)
                        @php
                            $empRevenue = $employee->total_revenue ?? 0;
                            $empSalary = $empRevenue * 0.4;
                            $empPaid = $employee->total_paid ?? 0;
                            $empDue = max(0, $empSalary - $empPaid);
                        @endphp

                        <!-- 1. Pay Salary Modal -->
                        <div class="modal fade" id="payModal-{{ $employee->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content premium-card border-secondary border-opacity-25 text-light"
                                    style="background: #181c22;">
                                    <div class="modal-header border-secondary border-opacity-10">
                                        <h5 class="modal-title fw-bold text-white">
                                            <i class="bi bi-wallet-fill text-success me-2"></i>Pay Salary to
                                            {{ $employee->name }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.reports.employee.pay-salary') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                        <div class="modal-body p-4">
                                            <div class="p-3 mb-3 rounded-3"
                                                style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                                    <span>Total 40% Salary:</span>
                                                    <span class="text-warning font-monospace">AED
                                                        {{ number_format($empSalary, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                                    <span>Already Paid:</span>
                                                    <span class="text-info font-monospace">AED
                                                        {{ number_format($empPaid, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between small fw-bold">
                                                    <span class="text-light">Current Due Balance:</span>
                                                    <span class="text-danger font-monospace">AED
                                                        {{ number_format($empDue, 2) }}</span>
                                                </div>
                                            </div>



                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-semibold">Payment Amount
                                                    (AED)</label>
                                                <input type="number" step="0.01" min="0.01"
                                                    max="{{ $empDue }}" name="amount"
                                                    class="form-control filter-input"
                                                    value="{{ $empDue > 0 ? $empDue : '' }}"
                                                    placeholder="Maximum payable: AED {{ number_format($empDue, 2) }}"
                                                    required>
                                                <small class="text-secondary d-block mt-1">
                                                    * Maximum payable amount: <span class="text-danger font-monospace">AED
                                                        {{ number_format($empDue, 2) }}</span>
                                                </small>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small fw-semibold">Payment
                                                        Date</label>
                                                    <input type="date" name="payment_date"
                                                        class="form-control filter-input" value="{{ date('Y-m-d') }}"
                                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-muted small fw-semibold">Method</label>
                                                    <select name="payment_method" class="form-select filter-input">
                                                        <option value="Cash">Cash</option>
                                                        <option value="Bank Transfer">Bank Transfer</option>
                                                        <option value="Cheque">Cheque</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label text-muted small fw-semibold">Note / Remarks
                                                    (Optional)</label>
                                                <input type="text" name="note" class="form-control filter-input"
                                                    placeholder="e.g. October 1st half salary payout">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-secondary border-opacity-10">
                                            <button type="button" class="btn btn-outline-secondary rounded-3"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success px-4 rounded-3 fw-semibold">
                                                <i class="bi bi-check2-circle me-1"></i> Disburse Payment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Payment History Modal -->
                        <div class="modal fade" id="paymentHistoryModal-{{ $employee->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content premium-card border-secondary border-opacity-25 text-light"
                                    style="background: #181c22;">
                                    <div class="modal-header border-secondary border-opacity-10">
                                        <h5 class="modal-title fw-bold text-white">
                                            <i class="bi bi-clock-history text-info me-2"></i>Payment History:
                                            {{ $employee->name }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="table-responsive">
                                            <table class="table premium-table align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount Paid</th>
                                                        <th>Method</th>
                                                        <th>Note / Ref</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($employee->salaryPayments as $payment)
                                                        <tr>
                                                            <td class="font-monospace small">
                                                                {{ $payment->payment_date->format('d M, Y') }}</td>
                                                            <td class="font-monospace text-success fw-bold">AED
                                                                {{ number_format($payment->amount, 2) }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-secondary bg-opacity-25 text-light font-monospace">{{ $payment->payment_method }}</span>
                                                            </td>
                                                            <td class="text-muted small">
                                                                {{ $payment->note ?? 'Salary Payment' }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center py-4 text-muted">No
                                                                salary payout history recorded yet.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. View Service Entries Modal -->
                        <div class="modal fade" id="employeeDetailsModal-{{ $employee->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content premium-card border-secondary border-opacity-25 text-light"
                                    style="background: #181c22;">
                                    <div class="modal-header border-secondary border-opacity-10">
                                        <h5 class="modal-title fw-bold text-white">
                                            <i class="bi bi-person-fill text-warning me-2"></i>Service Entries by
                                            {{ $employee->name }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="table-responsive">
                                            <table class="table premium-table align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Entry #</th>
                                                        <th>Date</th>
                                                        <th>Customer</th>
                                                        <th>Services</th>
                                                        <th class="text-end">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($employee->serviceEntries as $entry)
                                                        <tr>
                                                            <td class="font-monospace text-muted">
                                                                #INV-{{ str_pad($entry->id, 5, '0', STR_PAD_LEFT) }}</td>
                                                            <td class="small">
                                                                {{ \Carbon\Carbon::parse($entry->entry_date)->format('d M, Y') }}
                                                            </td>
                                                            <td>
                                                                {{ $entry->customer->name ?? 'Walk-in' }}
                                                                <small
                                                                    class="d-block text-muted">{{ $entry->customer->phone ?? '' }}</small>
                                                            </td>
                                                            <td>
                                                                @foreach ($entry->items as $item)
                                                                    <span
                                                                        class="badge bg-dark border border-secondary border-opacity-25 text-warning font-monospace">
                                                                        {{ $item->service->name ?? 'Service' }}
                                                                    </span>
                                                                @endforeach
                                                            </td>
                                                            <td class="text-end font-monospace text-success fw-bold">AED
                                                                {{ number_format($entry->total_amount, 2) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-4 text-muted">No
                                                                entry records found.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @show
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('reportFilterForm');
        const searchInput = document.getElementById('liveSearchInput');
        const autoTriggers = document.querySelectorAll('.auto-trigger');
        const resetBtn = document.getElementById('resetFiltersBtn');
        const tableContainer = document.getElementById('reportTableContainer');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const metricEntries = document.getElementById('metricTotalEntries');
        const metricRevenue = document.getElementById('metricTotalRevenue');
        const metricSalary = document.getElementById('metricTotalSalary');
        const metricPaid = document.getElementById('metricTotalPaid');

        let debounceTimer;

        function fetchReport(url = "{{ route('admin.reports.employee') }}") {
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
                    metricEntries.textContent = data.totalEntries;
                    metricRevenue.textContent = data.totalRevenue;
                    if (metricSalary && data.totalSalary) {
                        metricSalary.textContent = data.totalSalary;
                    }
                    if (metricPaid && data.totalPaid) {
                        metricPaid.textContent = data.totalPaid;
                    }
                    attachPaginationEvents();
                })
                .catch(err => console.error("Error:", err))
                .finally(() => {
                    loadingSpinner.classList.add('d-none');
                });
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchReport();
            }, 350);
        });

        autoTriggers.forEach(input => {
            input.addEventListener('change', function() {
                fetchReport();
            });
        });

        resetBtn.addEventListener('click', function() {
            form.reset();
            fetchReport();
        });

        function attachPaginationEvents() {
            const paginationLinks = tableContainer.querySelectorAll('#ajaxPaginationWrapper a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    if (href) fetchReport(href);
                });
            });
        }

        attachPaginationEvents();
    });
</script>
@endpush
