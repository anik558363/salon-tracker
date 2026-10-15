@extends('layouts.adminlte')

@section('title', 'Add New Service')
@section('page_title', 'Add Service to Catalog')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="premium-card">
            <div class="premium-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-scissors text-primary me-2"></i>Create New Service Offering</h5>
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Catalog
                </a>
            </div>

            <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger bg-opacity-10 border-danger border-opacity-25 text-danger alert-dismissible fade show mb-4">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label text-muted small fw-semibold">Service Name</label>
                            <input type="text" name="name" class="form-control filter-input" value="{{ old('name') }}" placeholder="e.g. Royal Beard Grooming" required>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label text-muted small fw-semibold">Rate / Price (AED)</label>
                            <input type="number" step="0.01" name="price" class="form-control filter-input" value="{{ old('price') }}" placeholder="0.00" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">Short Description (Shows on Website Cards)</label>
                            <textarea name="description" rows="3" class="form-control filter-input" placeholder="Short description of what the service includes...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">Service Image (Optional)</label>
                            <input type="file" name="image" class="form-control filter-input" accept="image/*">
                            <small class="text-muted">Formats: JPG, PNG, WEBP (Max 2MB)</small>
                        </div>
                    </div>
                </div>

                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Save Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
