@extends('layouts.adminlte')

@section('title', 'Add New Staff')
@section('page_title', 'Add New Stylist')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="premium-card">
            <div class="premium-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-person-plus text-primary me-2"></i>New Stylist Registration</h5>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
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
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control filter-input" value="{{ old('name') }}" placeholder="e.g. Tanvir Hasan" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Email Address (Login)</label>
                            <input type="email" name="email" class="form-control filter-input" value="{{ old('email') }}" placeholder="staff@salon.com" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Initial Password</label>
                            <input type="password" name="password" class="form-control filter-input" placeholder="Min. 6 characters" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Designation Title (Shows on Website)</label>
                            <input type="text" name="title" class="form-control filter-input" value="{{ old('title') }}" placeholder="e.g. Executive Stylist, Senior Barber">
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">Short Bio / Specialization (Shows on Website)</label>
                            <textarea name="bio" rows="3" class="form-control filter-input" placeholder="Specialist in precision cutting, beard sculpting, and hair treatments...">{{ old('bio') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">Profile Photo (Optional)</label>
                            <input type="file" name="photo" class="form-control filter-input" accept="image/*">
                            <small class="text-muted">Upload square image (Max 2MB)</small>
                        </div>
                    </div>
                </div>

                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Register Stylist
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
