@extends('layouts.adminlte')

@section('title', 'Edit Staff Member')
@section('page_title', 'Edit Staff Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="premium-card">
            <div class="premium-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Stylist: {{ $employee->name }}</h5>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
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
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control filter-input" value="{{ old('name', $employee->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Email Address (Login)</label>
                            <input type="email" name="email" class="form-control filter-input" value="{{ old('email', $employee->email) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Designation Title (Shows on Website)</label>
                            <input type="text" name="title" class="form-control filter-input" value="{{ old('title', $employee->title) }}" placeholder="e.g. Master Stylist, Senior Barber">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Reset Password (Optional)</label>
                            <input type="password" name="password" class="form-control filter-input" placeholder="Leave empty to keep current password">
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">Short Bio / Specialization (Shows on Website)</label>
                            <textarea name="bio" rows="3" class="form-control filter-input" placeholder="Specialist in precision cutting, beard sculpting, and hair treatments...">{{ old('bio', $employee->bio) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">Profile Photo (Shows on Website)</label>
                            @if($employee->photo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $employee->photo) }}" class="rounded-circle border border-secondary border-opacity-25" style="width: 70px; height: 70px; object-fit: cover;">
                                </div>
                            @endif
                            <input type="file" name="photo" class="form-control filter-input" accept="image/*">
                            <small class="text-muted">Upload square image (Max 2MB)</small>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ $employee->status ? 'checked' : '' }}>
                                <label class="form-check-label text-muted small fw-semibold" for="statusSwitch">
                                    Active (Allows login and displays stylist in "Our Team" on landing page)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Update Stylist
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
