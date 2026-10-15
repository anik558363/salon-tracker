@extends('layouts.adminlte')

@section('title', 'Client Directory')
@section('page_title', 'Client Directory & History')

@section('content')
<!-- Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa;">
                <i class="bi bi-person-heart fs-2"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Clients</span>
                <h3 class="mb-0 fw-bold text-light">{{ \App\Models\Customer::count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(16, 185, 129, 0.12); color: #34d399;">
                <i class="bi bi-wallet2 fs-2"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Revenue From Clients</span>
                <h3 class="mb-0 fw-bold text-success">
                    AED {{ number_format(\App\Models\ServiceEntry::whereNotNull('customer_id')->sum('total_amount'), 2) }}
                </h3>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(245, 158, 11, 0.12); color: #fbbf24;">
                <i class="bi bi-calendar-event fs-2"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Client Visits</span>
                <h3 class="mb-0 fw-bold text-light">
                    {{ \App\Models\ServiceEntry::whereNotNull('customer_id')->count() }}
                </h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="premium-card mb-4">
            <!-- Filter & Search Bar -->
            <div class="p-4 border-bottom border-secondary border-opacity-10">
                <form action="{{ route('admin.customers.index') }}" method="GET">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text filter-input border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control filter-input border-start-0" placeholder="Search by client name or phone..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <select name="sort" class="form-select filter-input">
                                <option value="">Sort: Newest First</option>
                                <option value="spent_desc" {{ request('sort') === 'spent_desc' ? 'selected' : '' }}>Highest Total Spent</option>
                                <option value="visits_desc" {{ request('sort') === 'visits_desc' ? 'selected' : '' }}>Most Visits</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'sort']))
                                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3" title="Reset Filters">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Header -->
            <div class="premium-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-person-lines-fill text-primary me-2"></i>Client Directory</h5>
                    <small class="text-muted">Click "Service History" to view detailed service records for each client</small>
                </div>
            </div>

            <!-- Client Table -->
            <div class="table-responsive">
                <table class="table premium-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Client Name</th>
                            <th>Phone Number</th>
                            <th class="text-center">Total Visits</th>
                            <th class="text-end">Total Spent</th>
                            <th class="text-center pe-4">History</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">
                                {{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}
                            </td>
                            <td>
                                <div class="fw-bold text-light fs-6">{{ $customer->name ?? 'Walk-in Client' }}</div>
                                <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25" style="font-size: 0.7rem;">ID: #{{ $customer->id }}</span>
                            </td>
                            <td>
                                <span class="font-monospace text-muted">{{ $customer->phone ?? 'N/A' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge-services fs-6 px-3 py-1">
                                    {{ $customer->total_visits }} {{ Str::plural('visit', $customer->total_visits) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="badge-price fs-6">
                                    AED {{ number_format($customer->total_spent ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                @if($customer->total_visits > 0)
                                    <button class="btn btn-sm btn-outline-info rounded-3 px-3 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#history-{{ $customer->id }}">
                                        <i class="bi bi-clock-history me-1"></i> Service History ({{ $customer->total_visits }})
                                    </button>
                                @else
                                    <span class="text-muted small">No History</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Accordion for Service History -->
                        @if($customer->total_visits > 0)
                        <tr class="collapse" id="history-{{ $customer->id }}" style="background: rgba(0, 0, 0, 0.25);">
                            <td colspan="6" class="p-3">
                                <div class="p-3 rounded-3 border border-secondary border-opacity-25" style="background: #14171a;">
                                    <h6 class="text-info fw-bold mb-3"><i class="bi bi-scissors me-1"></i>Services provided for "{{ $customer->name ?? 'Client' }}":</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm text-light align-middle mb-0" style="font-size: 0.85rem;">
                                            <thead>
                                                <tr class="text-secondary border-bottom border-secondary border-opacity-25">
                                                    <th>Date & Time</th>
                                                    <th>Served By (Staff)</th>
                                                    <th>Services Rendered</th>
                                                    <th class="text-end">Total Bill</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($customer->serviceEntries as $entry)
                                                <tr class="border-bottom border-secondary border-opacity-10">
                                                    <td class="text-muted font-monospace">
                                                        {{ $entry->entry_date }} ({{ $entry->created_at->format('h:i A') }})
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light bg-opacity-10 text-light border border-secondary border-opacity-25">
                                                            {{ $entry->employee->name ?? 'Staff' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @foreach($entry->items as $item)
                                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 me-1">
                                                                {{ $item->service->name ?? 'Service' }} (AED {{ number_format($item->price, 2) }})
                                                            </span>
                                                        @endforeach
                                                    </td>
                                                    <td class="text-end fw-bold text-success">
                                                        AED {{ number_format($entry->total_amount, 2) }}
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
                                No client records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
