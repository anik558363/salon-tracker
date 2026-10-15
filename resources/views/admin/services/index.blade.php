@extends('layouts.adminlte')

@section('title', 'Service Management')
@section('page_title', 'Services & Rates')

@section('content')
<style>
    .premium-card {
        background: #1e2227;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    }
    .premium-header {
        background: rgba(255, 255, 255, 0.03);
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        padding: 18px 24px;
    }
    .filter-input {
        background: #14171a !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        color: #e4e6eb !important;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.9rem;
    }
    .filter-input:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
    }
    .premium-table {
        margin-bottom: 0;
        color: #e4e6eb;
    }
    .premium-table thead th {
        background: #181b1f !important;
        color: #9ca3af !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .premium-table tbody td {
        padding: 18px 20px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        background: transparent;
    }
    .premium-table tbody tr:hover td {
        background: rgba(255, 255, 255, 0.02) !important;
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
    .badge-active {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
    .badge-disabled {
        background: rgba(239, 68, 68, 0.12);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.2);
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
</style>

<!-- Stat Header Bar -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
                <i class="bi bi-scissors fs-3"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Services</span>
                <h4 class="mb-0 fw-bold text-light">{{ $services->total() }}</h4>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(16, 185, 129, 0.1); color: #34d399;">
                <i class="bi bi-check-circle-fill fs-3"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Active Services</span>
                <h4 class="mb-0 fw-bold text-light">{{ \App\Models\Service::where('status', true)->count() }}</h4>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="p-3 premium-card d-flex align-items-center gap-3">
            <div class="rounded-3 p-3" style="background: rgba(245, 158, 11, 0.1); color: #fbbf24;">
                <i class="bi bi-coin fs-3"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Average Price</span>
                <h4 class="mb-0 fw-bold text-light">AED {{ number_format(\App\Models\Service::avg('price') ?? 0, 2) }}</h4>
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
            <!-- Filter Section -->
            <div class="p-4 border-bottom border-secondary border-opacity-10">
                <form action="{{ route('admin.services.index') }}" method="GET">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-4 col-md-6">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control filter-input border-start-0" placeholder="Search service or description..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <select name="status" class="form-select filter-input">
                                <option value="">Filter by Status (All)</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Disabled Only</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <select name="sort" class="form-select filter-input">
                                <option value="">Sort: Newest First</option>
                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold">
                                <i class="bi bi-funnel me-1"></i> Apply
                            </button>
                            @if(request()->hasAny(['search', 'status', 'sort']))
                                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3" title="Clear Filters">
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
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Available Services List</h5>
                    <small class="text-muted">Manage your salon service catalog and active pricing</small>
                </div>
                <a href="{{ route('admin.services.create') }}" class="btn btn-primary rounded-3 px-3 py-2 shadow-sm fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Add Service
                </a>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table premium-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Image</th>
                            <th>Service Details</th>
                            <th class="text-center">Rate</th>
                            <th class="text-center">Status</th>
                            <th>Created On</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration + ($services->currentPage() - 1) * $services->perPage() }}</td>
                            <td>
                                @if($service->image)
                                    <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="rounded-3 border border-secondary border-opacity-25 object-fit-cover" style="width: 46px; height: 46px;">
                                @else
                                    <div class="rounded-3 border border-secondary border-opacity-25 d-flex align-items-center justify-content-center bg-dark text-warning" style="width: 46px; height: 46px;">
                                        <i class="bi bi-scissors fs-5"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-light fs-6 mb-1">{{ $service->name }}</div>
                                <div class="text-muted small" style="max-width: 280px; white-space: normal; line-height: 1.4;">
                                    {{ $service->description ?? 'No description provided.' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge-price">AED {{ number_format($service->price, 2) }}</span>
                            </td>
                            <td class="text-center">
                                @if($service->status)
                                    <span class="badge-active"><i class="bi bi-dot"></i> Active</span>
                                @else
                                    <span class="badge-disabled"><i class="bi bi-dot"></i> Disabled</span>
                                @endif
                            </td>
                            <td class="text-muted font-monospace small">{{ $service->created_at->format('d M, Y') }}</td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    <!-- Toggle Status Button -->
                                    <form action="{{ route('admin.services.toggle-status', $service->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm action-btn {{ $service->status ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $service->status ? 'Disable Service' : 'Enable Service' }}">
                                            <i class="bi {{ $service->status ? 'bi-eye-slash-fill' : 'bi-eye-fill' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Edit Button (Directs to edit page) -->
                                    <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-sm btn-outline-primary action-btn" title="Edit Service">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                @if(request('search'))
                                    No services matching "<strong>{{ request('search') }}</strong>".
                                @else
                                    No services found in database.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($services->hasPages())
                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


