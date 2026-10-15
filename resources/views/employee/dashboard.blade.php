@extends('layouts.adminlte')

@section('title', 'Staff Dashboard')
@section('page_title', 'My Daily Dashboard')

@section('content')
<style>
    .metric-card {
        background: #1e2227;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 22px 24px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    .metric-icon {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
    }
    .panel-card {
        background: #1e2227;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.25);
    }
    .panel-header {
        background: rgba(255, 255, 255, 0.02);
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        padding: 18px 24px;
    }
    .panel-table {
        margin-bottom: 0;
        color: #e4e6eb;
    }
    .panel-table thead th {
        background: #16191d !important;
        color: #9ca3af !important;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        padding: 15px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        font-weight: 600;
    }
    .panel-table tbody td {
        padding: 18px 20px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        background: transparent;
    }
    .panel-table tbody tr:hover td {
        background: rgba(255, 255, 255, 0.02) !important;
    }
    .badge-service-tag {
        background: rgba(59, 130, 246, 0.1);
        color: #93c5fd;
        border: 1px solid rgba(59, 130, 246, 0.2);
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
    }
    .badge-price {
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.9rem;
        border: 1px solid rgba(52, 211, 153, 0.2);
    }
</style>

<!-- Metric Cards Section -->
<div class="row g-4 mb-4">
    <!-- Today's Services -->
    <div class="col-12 col-md-6">
        <div class="metric-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold tracking-wider">Today's Total Services</span>
                <h2 class="mt-2 mb-0 fw-bold text-white">{{ $todayServiceCount }} <span class="fs-6 text-muted fw-normal">completed</span></h2>
            </div>
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa;">
                <i class="bi bi-scissors"></i>
            </div>
        </div>
    </div>

    <!-- Today's Earnings -->
    <div class="col-12 col-md-6">
        <div class="metric-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold tracking-wider">Today's Total Earnings</span>
                <h2 class="mt-2 mb-0 fw-bold text-success">AED {{ number_format($todayTotalAmount, 2) }}</h2>
            </div>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #34d399;">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>
</div>

<!-- History Table Card -->
<div class="row">
    <div class="col-12">
        <div class="panel-card mb-4">
            <div class="panel-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-clock-history text-primary me-2"></i>Today's Service History</h5>
                    <small class="text-muted">Review client entries and bills recorded by you today</small>
                </div>
                <a href="{{ route('employee.entries.create') }}" class="btn btn-primary rounded-3 px-3 py-2 shadow-sm fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> New Service Entry
                </a>
            </div>

            <div class="table-responsive">
                <table class="table panel-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Time</th>
                            <th>Client Information</th>
                            <th>Services Rendered</th>
                            <th class="text-end pe-4">Bill Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayEntries as $entry)
                        <tr>
                            <td class="ps-4 text-muted font-monospace small">
                                <span class="text-light fw-medium fs-6">{{ $entry->created_at->format('h:i A') }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-light fs-6 mb-1">{{ $entry->customer->name ?? 'Walk-in Client' }}</div>
                                @if($entry->customer?->phone)
                                    <span class="text-muted small font-monospace"><i class="bi bi-telephone me-1"></i>{{ $entry->customer->phone }}</span>
                                @else
                                    <span class="text-muted small">No contact provided</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($entry->items as $item)
                                        <span class="badge-service-tag">
                                            {{ $item->service->name }} <span class="text-muted">· AED {{ number_format($item->price, 2) }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge-price">AED {{ number_format($entry->total_amount, 2) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                No entries recorded for today yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
