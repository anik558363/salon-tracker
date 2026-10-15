@extends('layouts.adminlte')

@section('title', 'Admin Dashboard')
@section('page_title', 'Salon Overview & Monitoring Dashboard')

@section('content')
<style>
    .metric-card {
        background: #1e2227;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    .metric-icon {
        width: 52px;
        height: 52px;
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
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    .panel-header {
        background: rgba(255, 255, 255, 0.02);
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        padding: 16px 20px;
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
        padding: 14px 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .panel-table tbody td {
        padding: 16px 18px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        background: transparent;
    }
    .panel-table tbody tr:hover td {
        background: rgba(255, 255, 255, 0.02) !important;
    }
    .badge-services {
        background: rgba(255, 255, 255, 0.07);
        color: #d1d5db;
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
    }
    .badge-service-tag {
        background: rgba(59, 130, 246, 0.1);
        color: #93c5fd;
        border: 1px solid rgba(59, 130, 246, 0.2);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
    }
</style>

<!-- 4 Elegant Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Today's Income</span>
                <h3 class="mt-1 mb-0 fw-bold text-white">AED {{ number_format($todayIncome, 2) }}</h3>
            </div>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #34d399;">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">This Month's Income</span>
                <h3 class="mt-1 mb-0 fw-bold text-white">AED {{ number_format($thisMonthIncome, 2) }}</h3>
            </div>
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa;">
                <i class="bi bi-calendar-check"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Today's Services</span>
                <h3 class="mt-1 mb-0 fw-bold text-white">{{ $todayServicesCount }}</h3>
            </div>
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #fbbf24;">
                <i class="bi bi-scissors"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="metric-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Active Staff</span>
                <h3 class="mt-1 mb-0 fw-bold text-white">{{ $totalEmployees }}</h3>
            </div>
            <div class="metric-icon" style="background: rgba(168, 85, 247, 0.12); color: #c084fc;">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>
</div>

<!-- Top Services Chart Row -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="panel-card p-3">
            <div class="panel-header d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="mb-0 fw-bold text-white"><i class="bi bi-pie-chart-fill text-warning me-2"></i>Most Demanded Services (Top Sales)</h6>
                    <small class="text-muted">Visual analytics of the most frequently booked salon services</small>
                </div>
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Top Popular</span>
            </div>

            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div id="topServicesBarChart" style="min-height: 290px;"></div>
                </div>
                <div class="col-lg-5">
                    <div id="topServicesDonutChart" style="min-height: 290px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Staff Performance -->
    <div class="col-lg-6">
        <div class="panel-card h-100">
            <div class="panel-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-white"><i class="bi bi-person-lines-fill text-primary me-2"></i>Staff Performance & Revenue</h6>
                <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25">Real-time</span>
            </div>
            <div class="table-responsive">
                <table class="table panel-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-3">Staff Member</th>
                            <th class="text-center">Today's Services</th>
                            <th class="text-end">Today's Income</th>
                            <th class="text-end pe-3">Monthly Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employeeStats as $stat)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-semibold text-light">{{ $stat->name }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge-services">{{ $stat->today_entries_count }}</span>
                            </td>
                            <td class="text-end fw-bold {{ ($stat->today_income ?? 0) > 0 ? 'text-success' : 'text-muted' }}">
                                AED {{ number_format($stat->today_income ?? 0, 2) }}
                            </td>
                            <td class="text-end pe-3 fw-bold {{ ($stat->total_income ?? 0) > 0 ? 'text-primary' : 'text-muted' }}">
                                AED {{ number_format($stat->total_income ?? 0, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Service Entries -->
    <div class="col-lg-6">
        <div class="panel-card h-100">
            <div class="panel-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-white"><i class="bi bi-clock-history text-info me-2"></i>Recent Service Entries</h6>
                <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary border-opacity-25">Latest 10</span>
            </div>
            <div class="table-responsive">
                <table class="table panel-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-3">Time</th>
                            <th>Staff</th>
                            <th>Services</th>
                            <th class="text-end pe-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentEntries as $entry)
                        <tr>
                            <td class="ps-3">
                                <span class="text-light fw-medium">{{ $entry->created_at->format('h:i A') }}</span>
                                <div class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $entry->entry_date }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light bg-opacity-10 text-light border border-secondary border-opacity-25 px-2 py-1">
                                    {{ $entry->employee->name }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($entry->items as $item)
                                        <span class="badge-service-tag">{{ $item->service->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-end pe-3 fw-bold text-success">
                                AED {{ number_format($entry->total_amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary"></i>
                                No recent entries recorded yet.
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

@push('scripts')
<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartLabels = {!! json_encode($chartLabels) !!};
    const chartSeries = {!! json_encode($chartSeries) !!};

    if (chartSeries.length > 0 && chartSeries.some(val => val > 0)) {
        // 1. Horizontal Bar Chart (Sales Count per Service)
        const barOptions = {
            series: [{
                name: 'Times Booked',
                data: chartSeries
            }],
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                background: 'transparent'
            },
            theme: { mode: 'dark' },
            colors: ['#3b82f6'],
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    horizontal: true,
                    distributed: false,
                    barHeight: '55%'
                }
            },
            dataLabels: {
                enabled: true,
                formatter: val => val + " bookings"
            },
            xaxis: {
                categories: chartLabels,
                labels: { style: { colors: '#9ca3af' } }
            },
            yaxis: {
                labels: { style: { colors: '#e5e7eb', fontSize: '13px' } }
            },
            grid: {
                borderColor: 'rgba(255, 255, 255, 0.06)'
            },
            tooltip: {
                theme: 'dark'
            }
        };
        new ApexCharts(document.querySelector("#topServicesBarChart"), barOptions).render();

        // 2. Donut Chart (Proportion of Popularity)
        const donutOptions = {
            series: chartSeries,
            labels: chartLabels,
            chart: {
                type: 'donut',
                height: 280,
                background: 'transparent'
            },
            theme: { mode: 'dark' },
            colors: ['#3b82f6', '#10b981', '#f59e0b', '#a855f7', '#ec4899', '#06b6d4'],
            legend: {
                position: 'bottom',
                labels: { colors: '#d1d5db' }
            },
            stroke: {
                show: true,
                colors: ['#1e2227'],
                width: 3
            },
            dataLabels: {
                enabled: true
            },
            tooltip: {
                theme: 'dark'
            }
        };
        new ApexCharts(document.querySelector("#topServicesDonutChart"), donutOptions).render();
    } else {
        document.querySelector("#topServicesBarChart").innerHTML = '<div class="text-center py-5 text-muted"><i class="bi bi-bar-chart fs-1 d-block mb-2"></i>No service bookings recorded yet to render chart.</div>';
        document.querySelector("#topServicesDonutChart").innerHTML = '';
    }
</script>
@endpush
