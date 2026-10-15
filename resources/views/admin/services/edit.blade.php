@extends('layouts.adminlte')

@section('title', 'Edit Service')
@section('page_title', 'Edit Salon Service')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-warning card-outline shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Update Service: {{ $service->name }}</h5>
            </div>
            <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Service Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $service->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Standard Price (AED) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">AED</span>
                            <input type="number" step="0.50" min="0" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $service->price) }}" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', $service->status) ? 'selected' : '' }}>Active (Available in billing form)</option>
                            <option value="0" {{ !old('status', $service->status) ? 'selected' : '' }}>Disabled (Hidden from staff)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i> Update Service</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
