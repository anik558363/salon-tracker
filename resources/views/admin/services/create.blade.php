@extends('layouts.adminlte')

@section('title', 'Add New Service')
@section('page_title', 'Add Salon Service')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Create Service Offering</h5>
            </div>
            <form action="{{ route('admin.services.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Service Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Keratin Hair Treatment" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Standard Price (AED) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">AED</span>
                            <input type="number" step="0.50" min="0" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="0.00" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active (Available in billing form)</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Disabled (Hidden from staff)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Back to List</a>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Save Service</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
