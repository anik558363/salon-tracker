@extends('layouts.adminlte')

@section('title', 'Edit Service')
@section('page_title', 'Edit Service Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="premium-card">
            <div class="premium-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Service: {{ $service->name }}</h5>
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Catalog
                </a>
            </div>

            <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                            <input type="text" name="name" class="form-control filter-input" value="{{ old('name', $service->name) }}" required>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label text-muted small fw-semibold">Rate / Price (AED)</label>
                            <input type="number" step="0.01" name="price" class="form-control filter-input" value="{{ old('price', $service->price) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">Short Description (Shows on Website Cards)</label>
                            <textarea name="description" rows="3" class="form-control filter-input" placeholder="Enter service description...">{{ old('description', $service->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">Service Image</label>
                            @if($service->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $service->image) }}" class="rounded-3 border border-secondary border-opacity-25" style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control filter-input" accept="image/*">
                            <small class="text-muted">Upload to replace the current image (Max 2MB: JPG, PNG, WEBP)</small>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ $service->status ? 'checked' : '' }}>
                                <label class="form-check-label text-muted small fw-semibold" for="statusSwitch">
                                    Active (Service available for billing and visible on public website)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Update Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
