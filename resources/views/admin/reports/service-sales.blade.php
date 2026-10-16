@extends('layouts.adminlte')

@section('title', 'Service Sales & Customer Breakdown')
@section('page_title', 'Service Sales & Customer Reports')

@section('content')
<!-- Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa;">
                <i class="bi bi-scissors fs-2"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Services Rendered</span>
                <h3 class="mb-0 fw-bold text-light" id="metricTotalSold">{{ number_format($overallTotalSold) }} <span class="fs-6 text-muted fw-normal">times</span></h3>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(16, 185, 129, 0.12); color: #34d399;">
                <i class="bi bi-cash-stack fs-2"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Revenue Generated</span>
                <h3 class="mb-0 fw-bold text-success" id="metricTotalRevenue">AED {{ number_format($overallTotalRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="premium-card mb-4">
            <!-- Filter & Search Bar (AJAX Controlled) -->
            <div class="p-4 border-bottom border-secondary border-opacity-10">
                <form id="serviceReportFilterForm" onsubmit="return false;">
                    <div class="row g-3 align-items-end">
                        <!-- Keyboard Live Search Input -->
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label text-muted small mb-1">Search Service</label>
                            <div class="input-group">

                                <input type="text" id="liveSearchInput" name="search" class="form-control filter-input" placeholder="Type service name..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Service Dropdown Filter -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-muted small mb-1">Select Service</label>
                            <select name="service_id" class="form-select filter-input auto-trigger">
                                <option value="">All Services Offering</option>
                                @foreach($allServices as $srv)
                                    <option value="{{ $srv->id }}" {{ request('service_id') == $srv->id ? 'selected' : '' }}>
                                        {{ $srv->name }} (AED {{ number_format($srv->price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Period Presets -->
                        <div class="col-lg-2 col-md-4">
                            <label class="form-label text-muted small mb-1">Time Period</label>
                            <select name="period" class="form-select filter-input auto-trigger">
                                <option value="">Custom Date Range</option>
                                <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today</option>
                                <option value="this_week" {{ request('period') === 'this_week' ? 'selected' : '' }}>This Week</option>
                                <option value="this_month" {{ request('period') === 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="this_year" {{ request('period') === 'this_year' ? 'selected' : '' }}>This Year</option>
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div class="col-lg-2 col-md-4">
                            <label class="form-label text-muted small mb-1">Start Date</label>
                            <input type="date" name="start_date" class="form-control filter-input auto-trigger" value="{{ $startDate }}">
                        </div>

                        <!-- End Date -->
                        <div class="col-lg-2 col-md-4">
                            <label class="form-label text-muted small mb-1">End Date</label>
                            <input type="date" name="end_date" class="form-control filter-input auto-trigger" value="{{ $endDate }}">
                        </div>

                        <div class="col-1 d-flex justify-content-end gap-2 mt-2">
                            <div id="loadingSpinner" class="spinner-border spinner-border-sm text-warning me-2 d-none" role="status"></div>
                            <button type="button" id="resetFiltersBtn" class="btn btn-outline-secondary px-4 py-2 rounded-3 ">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Header -->
            <div class="premium-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-people-fill text-primary me-2"></i>Service & Client Breakdown</h5>
                    <small class="text-muted">Click "Client History" on any service to view customers who availed it</small>
                </div>
            </div>

            <!-- Table Container for AJAX -->
            <div class="table-responsive" id="serviceReportTableContainer">
                @section('table_content')
                <table class="table premium-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Service Offering</th>
                            <th class="text-center">Times Availed</th>
                            <th class="text-end">Total Revenue</th>
                            <th class="text-center pe-4">Client Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceSales as $service)
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration + ($serviceSales->currentPage() - 1) *$serviceSales->perPage() }}</td>
                            <td>
                                <div class="fw-bold text-light fs-6 mb-1">{{ $service->name }}</div>
                                <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25" style="font-size: 0.72rem;">
                                    Base Price: AED {{ number_format($service->price, 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge-services fs-6 px-3 py-1">
                                    <i class="bi bi-scissors me-1 text-primary"></i>{{ $service->total_sold_count ?? 0 }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="badge-price fs-6">AED {{ number_format($service->total_revenue ?? 0, 2) }}</span>
                            </td>
                            <td class="text-center pe-4">
                                @if(($service->total_sold_count ?? 0) > 0)
                                    <button class="btn btn-sm btn-outline-info rounded-3 px-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#clients-{{ $service->id }}">
                                        <i class="bi bi-person-lines-fill me-1"></i> Client History ({{ $service->total_sold_count }})
                                    </button>
                                @else
                                    <span class="text-muted small">No Clients</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Accordion Row For Customer Data -->
                        @if(($service->total_sold_count ?? 0) > 0)
                        <tr class="collapse" id="clients-{{ $service->id }}" style="background: rgba(0, 0, 0, 0.25);">
                            <td colspan="5" class="p-3">
                                <div class="p-3 rounded-3 border border-secondary border-opacity-25" style="background: #14171a;">
                                    <h6 class="text-info fw-bold mb-3"><i class="bi bi-clock-history me-1"></i>Clients who availed "{{ $service->name }}":</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm text-light align-middle mb-0" style="font-size: 0.85rem;">
                                            <thead>
                                                <tr class="text-secondary border-bottom border-secondary border-opacity-25">
                                                    <th>Date & Time</th>
                                                    <th>Client Name</th>
                                                    <th>Client Phone</th>
                                                    <th>Served By (Staff)</th>
                                                    <th class="text-end">Charged Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($service->entryItems as $item)
                                                <tr class="border-bottom border-secondary border-opacity-10">
                                                    <td class="text-muted font-monospace">
                                                        {{ $item->serviceEntry->entry_date ? \Carbon\Carbon::parse($item->serviceEntry->entry_date)->format('d M, Y') : '' }}
                                                        ({{ $item->serviceEntry->created_at ? $item->serviceEntry->created_at->format('h:i A') : '' }})
                                                    </td>
                                                    <td class="fw-semibold text-white">
                                                        {{ $item->serviceEntry->customer->name ?? 'Walk-in Client' }}
                                                    </td>
                                                    <td class="text-muted">
                                                        {{ $item->serviceEntry->customer->phone ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light bg-opacity-10 text-light border border-secondary border-opacity-25">
                                                            {{ $item->serviceEntry->employee->name ?? 'Staff' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end fw-bold text-success font-monospace">
                                                        AED {{ number_format($item->price, 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif

                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                No service records found for the selected filters.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($serviceSales->hasPages())
                    <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end" id="ajaxPaginationWrapper">
                        {{ $serviceSales->links('pagination::bootstrap-5') }}
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
        const form = document.getElementById('serviceReportFilterForm');
        const searchInput = document.getElementById('liveSearchInput');
        const autoTriggers = document.querySelectorAll('.auto-trigger');
        const resetBtn = document.getElementById('resetFiltersBtn');
        const tableContainer = document.getElementById('serviceReportTableContainer');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const metricSold = document.getElementById('metricTotalSold');
        const metricRevenue = document.getElementById('metricTotalRevenue');

        let debounceTimer;

        function fetchServiceReport(url = "{{ route('admin.reports.service-sales') }}") {
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
                if (metricSold && data.overallTotalSold) {
                    metricSold.innerHTML = `${data.overallTotalSold}`;
                }
                if (metricRevenue && data.overallTotalRevenue) {
                    metricRevenue.textContent = data.overallTotalRevenue;
                }
                attachPaginationEvents();
            })
            .catch(err => console.error("AJAX Error:", err))
            .finally(() => {
                loadingSpinner.classList.add('d-none');
            });
        }

        // Live keyboard search with debounce
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchServiceReport();
            }, 350);
        });

        // Dropdown changes trigger AJAX immediately
        autoTriggers.forEach(input => {
            input.addEventListener('change', function () {
                fetchServiceReport();
            });
        });

        // Reset button
        resetBtn.addEventListener('click', function () {
            form.reset();
            fetchServiceReport();
        });

        // AJAX pagination links
        function attachPaginationEvents() {
            const paginationLinks = tableContainer.querySelectorAll('#ajaxPaginationWrapper a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    if (href) {
                        fetchServiceReport(href);
                    }
                });
            });
        }

        attachPaginationEvents();
    });
</script>
@endpush
