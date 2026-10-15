@extends('layouts.adminlte')

@section('title', 'Edit Staff Member')
@section('page_title', 'Edit Staff Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card card-warning card-outline shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Update Details: {{ $employee->name }}</h5>
            </div>
            <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address (Login) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password <small class="text-muted">(Leave blank to keep existing)</small></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter new password if changing">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', $employee->status) ? 'selected' : '' }}>Active (Permitted to login)</option>
                            <option value="0" {{ !old('status', $employee->status) ? 'selected' : '' }}>Inactive (Access blocked)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i> Update Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
