@extends('layouts.adminlte')

@section('title', 'Staff Management')
@section('page_title', 'Staff Members')

@section('content')
<!-- Staff Stats Bar -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
                <i class="bi bi-people-fill fs-3"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Staff</span>
                <h4 class="mb-0 fw-bold text-light font-monospace" id="metricTotalStaff">{{ $totalStaffCount ?? \App\Models\User::role('employee')->count() }}</h4>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(16, 185, 129, 0.1); color: #34d399;">
                <i class="bi bi-person-check-fill fs-3"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Active Staff</span>
                <h4 class="mb-0 fw-bold text-light font-monospace" id="metricActiveStaff">{{ $activeStaffCount ?? \App\Models\User::role('employee')->where('status', true)->count() }}</h4>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(239, 68, 68, 0.1); color: #f87171;">
                <i class="bi bi-person-x-fill fs-3"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Inactive Staff</span>
                <h4 class="mb-0 fw-bold text-light font-monospace" id="metricInactiveStaff">{{ $inactiveStaffCount ?? \App\Models\User::role('employee')->where('status', false)->count() }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success bg-opacity-10 border-success border-opacity-25 text-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="premium-card mb-4">
            <!-- Filter & Search Section (AJAX) -->
            <div class="p-4 border-bottom border-secondary border-opacity-10">
                <form id="employeeFilterForm" onsubmit="return false;">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-6 col-md-6">
                            <div class="input-group">
                               
                                <input type="text" id="liveSearchInput" name="search" class="form-control filter-input" placeholder="Search by staff name or email..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4">
                            <select name="status" class="form-select filter-input auto-trigger">
                                <option value="">Filter by Status (All)</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive Only</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-2 d-flex justify-content-end align-items-center gap-2">
                            <div id="loadingSpinner" class="spinner-border spinner-border-sm text-warning d-none" role="status"></div>
                            <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100 py-2 rounded-3 fw-semibold" title="Reset Filters">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Header -->
            <div class="premium-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-people-fill text-primary me-2"></i>Staff Directory</h5>
                    <small class="text-muted">Manage employee accounts, credentials, and salon access</small>
                </div>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary rounded-3 px-3 py-2 shadow-sm fw-semibold">
                    <i class="bi bi-person-plus me-1"></i> Add New Staff
                </a>
            </div>

            <!-- Table Container for AJAX -->
            <div class="table-responsive" id="employeeTableContainer">
                @section('table_content')
                <table class="table premium-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Photo</th>
                            <th>Staff Member</th>
                            <th>Title / Designation</th>
                            <th class="text-center">Status</th>
                            <th>Joined Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration + ($employees->currentPage() - 1) *$employees->perPage() }}</td>
                            <td>
                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->name }}" class="rounded-circle border border-secondary border-opacity-25 object-fit-cover" style="width: 44px; height: 44px;">
                                @else
                                    <div class="rounded-circle border border-secondary border-opacity-25 d-flex align-items-center justify-content-center bg-dark text-warning" style="width: 44px; height: 44px;">
                                        <i class="bi bi-person-fill fs-5"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-light fs-6">{{ $employee->name }}</div>
                                <div class="text-muted small font-monospace">{{ $employee->email }}</div>
                                @if($employee->bio)
                                    <small class="text-secondary text-truncate d-block mt-1" style="max-width: 200px;">
                                        {{ $employee->bio }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                                    {{ $employee->title ?? 'Executive Stylist' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($employee->status)
                                    <span class="badge-active"><i class="bi bi-dot"></i> Active</span>
                                @else
                                    <span class="badge-disabled"><i class="bi bi-dot"></i> Inactive</span>
                                @endif
                            </td>
                            <td class="text-muted font-monospace small">{{ $employee->created_at->format('d M, Y') }}</td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    <!-- Toggle Status Button -->
                                    <form action="{{ route('admin.employees.toggle-status', $employee->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm action-btn {{ $employee->status ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $employee->status ? 'Deactivate Staff' : 'Activate Staff' }}">
                                            <i class="bi {{ $employee->status ? 'bi-person-slash' : 'bi-person-check' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Edit Button (Directs to edit page) -->
                                    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-primary action-btn" title="Edit Staff Details">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                                No staff records found matching criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($employees->hasPages())
                    <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end" id="ajaxPaginationWrapper">
                        {{ $employees->links('pagination::bootstrap-5') }}
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
        const form = document.getElementById('employeeFilterForm');
        const searchInput = document.getElementById('liveSearchInput');
        const autoTriggers = document.querySelectorAll('.auto-trigger');
        const resetBtn = document.getElementById('resetBtn');
        const tableContainer = document.getElementById('employeeTableContainer');
        const loadingSpinner = document.getElementById('loadingSpinner');

        const metricTotal = document.getElementById('metricTotalStaff');
        const metricActive = document.getElementById('metricActiveStaff');
        const metricInactive = document.getElementById('metricInactiveStaff');

        let debounceTimer;

        function fetchEmployees(url = "{{ route('admin.employees.index') }}") {
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
                if (metricTotal && data.totalStaff) {
                    metricTotal.textContent = data.totalStaff;
                }
                if (metricActive && data.activeStaff) {
                    metricActive.textContent = data.activeStaff;
                }
                if (metricInactive && data.inactiveStaff) {
                    metricInactive.textContent = data.inactiveStaff;
                }
                attachPaginationEvents();
            })
            .catch(err => console.error("AJAX Error:", err))
            .finally(() => {
                loadingSpinner.classList.add('d-none');
            });
        }

        // কী-বোর্ডে টাইপ করার সাথে সাথে লাইভ সার্চ (350ms Debounced)
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchEmployees();
            }, 350);
        });

        // স্ট্যাটাস ড্রপডাউন সিলেক্ট করলেই সাথে সাথে অটো লোড
        autoTriggers.forEach(input => {
            input.addEventListener('change', function () {
                fetchEmployees();
            });
        });

        // ফিল্টার রিসেট বাটন
        resetBtn.addEventListener('click', function () {
            form.reset();
            fetchEmployees();
        });

        // পেজিনেশন লিঙ্কগুলোতে ক্লিক করলে পেজ রিফ্রেশ ছাড়া লোড
        function attachPaginationEvents() {
            const paginationLinks = tableContainer.querySelectorAll('#ajaxPaginationWrapper a');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    if (href) {
                        fetchEmployees(href);
                    }
                });
            });
        }

        attachPaginationEvents();
    });
</script>
@endpush
