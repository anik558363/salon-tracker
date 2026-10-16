@extends('layouts.adminlte')

@section('title', 'Income & Net Profit Report')
@section('page_title', 'Income & Net Profit Report')

@section('content')
<!-- Filter Bar -->
<div class="row mb-4">
    <div class="col-12">
        <div class="premium-card p-4">
            <form id="incomeFilterForm" onsubmit="return false;">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-semibold">Preset Period</label>
                        <select name="period" class="form-select filter-input auto-trigger">
                            <option value="">Custom Date Range</option>
                            <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="this_week" {{ request('period') === 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="this_month" {{ request('period') === 'this_month' || !request()->hasAny(['period','start_date']) ? 'selected' : '' }}>This Month</option>
                            <option value="this_year" {{ request('period') === 'this_year' ? 'selected' : '' }}>This Year</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-semibold">Start Date</label>
                        <input type="date" name="start_date" class="form-control filter-input auto-trigger" value="{{ $startDate }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-semibold">End Date</label>
                        <input type="date" name="end_date" class="form-control filter-input auto-trigger" value="{{ $endDate }}">
                    </div>

                    <div class="col-md-3 d-flex justify-content-end gap-2">
                        <div id="loadingSpinner" class="spinner-border spinner-border-sm text-warning d-none align-self-center" role="status"></div>
                        <button type="button" id="resetBtn" class="btn btn-outline-secondary px-3 py-2 rounded-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formula Overview Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="p-3 rounded-3 border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.02);">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-light font-monospace small">
                <span><i class="bi bi-calculator me-2 text-warning"></i><strong>Calculation Formula:</strong></span>
                <span class="text-success fw-bold">Total Revenue</span>
                <span class="text-muted">-</span>
                <span class="text-muted">(</span>
                <span class="text-danger">Total Expenses</span>
                <span class="text-muted">+</span>
                <span class="text-warning">Staff Payouts</span>
                <span class="text-muted">)</span>
                <span class="text-muted">=</span>
                <span class="badge bg-primary px-3 py-2 fs-6">Net Income / Profit</span>
            </div>
        </div>
    </div>
</div>

<!-- Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(16, 185, 129, 0.1); color: #34d399;">
                <i class="bi bi-cash-stack fs-2"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Revenue</span>
                <h4 class="mb-0 fw-bold text-success font-monospace" id="metricRevenue">AED {{ number_format($totalRevenue, 2) }}</h4>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(239, 68, 68, 0.1); color: #f87171;">
                <i class="bi bi-receipt-cutoff fs-2"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Expenses</span>
                <h4 class="mb-0 fw-bold text-danger font-monospace" id="metricExpenses">AED {{ number_format($totalExpenses, 2) }}</h4>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(245, 158, 11, 0.1); color: #fbbf24;">
                <i class="bi bi-wallet2 fs-2"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Staff Payouts</span>
                <h4 class="mb-0 fw-bold text-warning font-monospace" id="metricPayouts">AED {{ number_format($totalPayouts, 2) }}</h4>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: {{ $netIncome >= 0 ? 'rgba(59, 130, 246, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{$netIncome >= 0 ? '#60a5fa' : '#f87171' }};">
                <i class="bi {{ $netIncome >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }} fs-2"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Net Income (Profit)</span>
                <h4 class="mb-0 fw-bold {{ $netIncome >= 0 ? 'text-primary' : 'text-danger' }} font-monospace" id="metricNetIncome">
                    AED {{ number_format($netIncome, 2) }}
                </h4>
            </div>
        </div>
    </div>
</div>

<!-- Breakdown Summary -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="premium-card p-4 h-100">
            <h5 class="fw-bold text-white mb-3"><i class="bi bi-pie-chart text-primary me-2"></i>Profitability Overview</h5>
            <div class="table-responsive">
                <table class="table premium-table align-middle">
                    <tbody>
                        <tr>
                            <td>Gross Service Revenue</td>
                            <td class="text-end font-monospace text-success fw-bold" id="detailRevenue">+ AED {{ number_format($totalRevenue, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Operating Expenses Outflow</td>
                            <td class="text-end font-monospace text-danger" id="detailExpenses">- AED {{ number_format($totalExpenses, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Staff Commission/Salary Disbursed</td>
                            <td class="text-end font-monospace text-warning" id="detailPayouts">- AED {{ number_format($totalPayouts, 2) }}</td>
                        </tr>
                        <tr class="border-top border-secondary">
                            <td class="fw-bold text-white">Total Outflow (Expenses + Payouts)</td>
                            <td class="text-end font-monospace text-danger fw-bold" id="detailOutflow">- AED {{ number_format($totalOutflow, 2) }}</td>
                        </tr>
                        <tr class="border-top border-secondary">
                            <td class="fw-bold text-white fs-6">Net Retained Profit / Income</td>
                            <td class="text-end font-monospace fw-bold fs-5 {{ $netIncome >= 0 ? 'text-primary' : 'text-danger' }}" id="detailNetIncome">
                                AED {{ number_format($netIncome, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="premium-card p-4 h-100">
            <h5 class="fw-bold text-white mb-3"><i class="bi bi-clock-history text-info me-2"></i>Recent Outflow Activity</h5>
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active py-1 px-3 small" id="pills-exp-tab" data-bs-toggle="pill" data-bs-target="#pills-exp" type="button">Recent Expenses</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-1 px-3 small" id="pills-pay-tab" data-bs-toggle="pill" data-bs-target="#pills-pay" type="button">Recent Payouts</button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-exp">
                    <div class="table-responsive">
                        <table class="table table-sm text-light align-middle mb-0" style="font-size: 0.82rem;">
                            <tbody>
                                @forelse($expenses as $exp)
                                <tr>
                                    <td class="text-muted">{{ $exp->expense_date->format('d M') }}</td>
                                    <td>{{ $exp->name }}</td>
                                    <td><span class="badge bg-secondary bg-opacity-25">{{ $exp->category->name ?? 'General' }}</span></td>
                                    <td class="text-end text-danger font-monospace">AED {{ number_format($exp->amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No expenses recorded for period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-pay">
                    <div class="table-responsive">
                        <table class="table table-sm text-light align-middle mb-0" style="font-size: 0.82rem;">
                            <tbody>
                                @forelse($payouts as $pay)
                                <tr>
                                    <td class="text-muted">{{ $pay->payment_date->format('d M') }}</td>
                                    <td>{{ $pay->employee->name ?? 'Stylist' }}</td>
                                    <td><span class="badge bg-dark border border-secondary">{{ $pay->payment_method }}</span></td>
                                    <td class="text-end text-warning font-monospace">AED {{ number_format($pay->amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No salary payouts recorded for period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('incomeFilterForm');
        const autoTriggers = document.querySelectorAll('.auto-trigger');
        const resetBtn = document.getElementById('resetBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');

        function fetchIncomeData() {
            loadingSpinner.classList.remove('d-none');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            fetch(`{{ route('admin.reports.income') }}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('metricRevenue').textContent = data.totalRevenue;
                document.getElementById('metricExpenses').textContent = data.totalExpenses;
                document.getElementById('metricPayouts').textContent = data.totalPayouts;

                const netElem = document.getElementById('metricNetIncome');
                netElem.textContent = data.netIncome;
                netElem.className = `mb-0 fw-bold font-monospace ${data.isProfit ? 'text-primary' : 'text-danger'}`;

                document.getElementById('detailRevenue').textContent = `+ ${data.totalRevenue}`;
                document.getElementById('detailExpenses').textContent = `- ${data.totalExpenses}`;
                document.getElementById('detailPayouts').textContent = `- ${data.totalPayouts}`;
                document.getElementById('detailOutflow').textContent = `- ${data.totalOutflow}`;

                const detailNet = document.getElementById('detailNetIncome');
                detailNet.textContent = data.netIncome;
                detailNet.className = `text-end font-monospace fw-bold fs-5 ${data.isProfit ? 'text-primary' : 'text-danger'}`;
            })
            .catch(err => console.error("Error:", err))
            .finally(() => {
                loadingSpinner.classList.add('d-none');
            });
        }

        autoTriggers.forEach(input => {
            input.addEventListener('change', fetchIncomeData);
        });

        resetBtn.addEventListener('click', function () {
            form.reset();
            fetchIncomeData();
        });
    });
</script>
@endpush
