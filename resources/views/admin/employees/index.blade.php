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
                <h4 class="mb-0 fw-bold text-light">{{ \App\Models\User::role('employee')->count() }}</h4>
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
                <h4 class="mb-0 fw-bold text-light">{{ \App\Models\User::role('employee')->where('status', true)->count() }}</h4>
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
                <h4 class="mb-0 fw-bold text-light">{{ \App\Models\User::role('employee')->where('status', false)->count() }}</h4>
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
            <!-- Filter & Search Section -->
            <div class="p-4 border-bottom border-secondary border-opacity-10">
                <form action="{{ route('admin.employees.index') }}" method="GET">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-6 col-md-6">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control filter-input border-start-0" placeholder="Search by staff name or email..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-3">
                            <select name="status" class="form-select filter-input">
                                <option value="">Filter by Status (All)</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive Only</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3" title="Clear Filter">
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
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-people-fill text-primary me-2"></i>Staff Directory</h5>
                    <small class="text-muted">Manage employee accounts, credentials, and salon access</small>
                </div>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary rounded-3 px-3 py-2 shadow-sm fw-semibold">
                    <i class="bi bi-person-plus me-1"></i> Add New Staff
                </a>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table premium-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Staff Member</th>
                            <th>Email Address</th>
                            <th class="text-center">Status</th>
                            <th>Joined Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}</td>
                            <td>
                                <div class="fw-bold text-light fs-6">{{ $employee->name }}</div>
                            </td>
                            <td>
                                <span class="text-muted">{{ $employee->email }}</span>
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

                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-primary action-btn" title="Edit Staff Details">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                                @if(request('search'))
                                    No staff member matching "<strong>{{ request('search') }}</strong>".
                                @else
                                    No staff records found in database.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($employees->hasPages())
                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
