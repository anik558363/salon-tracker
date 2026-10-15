@extends('layouts.adminlte')

@section('title', 'Employee Performance Report')
@section('page_title', 'Staff Service Entries & Revenue Report')

@section('content')
    <!-- Metric Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
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

        <div class="col-sm-6 col-md-4">
            <div class="p-3 premium-card d-flex align-items-center gap-3">
                <div class="rounded-3 p-3" style="background: rgba(16, 185, 129, 0.1); color: #34d399;">
                    <i class="bi bi-cash-stack fs-3"></i>
                </div>
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Total Revenue Generated</span>
                    <h4 class="mb-0 fw-bold text-success font-monospace" id="metricTotalRevenue">AED
                        {{ number_format($totalRevenue, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

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
                                    class="btn btn-outline-secondary  py-2 rounded-3 fw-semibold">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Report Header -->
                <div class="premium-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-white"><i class="bi bi-person-lines-fill text-warning me-2"></i>Stylist
                            Performance Table</h5>
                        <small class="text-muted">Showing total service entries, clients served, services rendered, and
                            revenue per employee</small>
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
                                <th class="text-center">TOTAL ENTRIES</th>
                                <th>SERVICES PROVIDED</th>
                                <th class="text-center">CLIENTS HANDLED</th>
                                <th class="text-end">TOTAL BILL AMOUNT</th>
                                <th class="text-center pe-4">DETAILS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                @php
                                    $renderedServices = $employee->serviceEntries
                                        ->flatMap(function ($entry) {
                                            return $entry->items->pluck('service.name');
                                        })
                                        ->filter()
                                        ->unique();

                                    $clientCount = $employee->serviceEntries
                                        ->pluck('customer_id')
                                        ->filter()
                                        ->unique()
                                        ->count();
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
                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 font-monospace fs-6">
                                            {{ $employee->total_entries ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1" style="max-width: 380px;">
                                            @forelse($renderedServices->take(3) as $srvName)
                                                <span
                                                    class="badge bg-dark border border-secondary border-opacity-25 text-warning font-monospace py-1 px-2">
                                                    {{ $srvName }}
                                                </span>
                                            @empty
                                                <span class="text-muted small fst-italic">No services rendered yet</span>
                                            @endforelse

                                            @if ($renderedServices->count() > 3)
                                                <span
                                                    class="badge bg-secondary bg-opacity-25 text-light font-monospace py-1 px-2">
                                                    +{{ $renderedServices->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">
                                            {{ $clientCount }} Clients
                                        </span>
                                    </td>
                                    <td class="text-end font-monospace fw-bold text-success fs-6">
                                        AED {{ number_format($employee->total_revenue ?? 0, 2) }}
                                    </td>
                                    <td class="text-center pe-4">
                                        <button class="btn btn-sm btn-outline-warning rounded-3" data-bs-toggle="modal"
                                            data-bs-target="#employeeDetailsModal-{{ $employee->id }}">
                                            <i class="bi bi-eye"></i> View Entries
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
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

                    <!-- Modals table-er baire rakha hoyeche jate layout na bhange -->
                    @foreach ($employees as $employee)
                        <div class="modal fade" id="employeeDetailsModal-{{ $employee->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content premium-card border-secondary border-opacity-25 text-light"
                                    style="background: #181c22;">
                                    <div class="modal-header border-secondary border-opacity-10">
                                        <h5 class="modal-title fw-bold text-white">
                                            <i class="bi bi-person-fill text-warning me-2"></i>Entries by
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
