@extends('layouts.adminlte')

@section('title', 'Staff Settings')
@section('page_title', 'My Profile & Security Settings')

@section('content')
<div class="row g-4 justify-content-center">
    <!-- 1. Profile Info (Nem & Imel) -->
    <div class="col-lg-6">
        <div class="premium-card h-100">
            <div class="premium-header">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-person-badge text-primary me-2"></i>My Profile Details</h5>
                <small class="text-muted">Update your full name and login email address</small>
            </div>

            <form action="{{ route('employee.settings.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-4">
                    @if(session('success_profile'))
                        <div class="alert alert-success bg-opacity-10 border-success border-opacity-25 text-success alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success_profile') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Staff Name</label>
                        <input type="text" name="name" class="form-control filter-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Email Address (Login)</label>
                        <input type="email" name="email" class="form-control filter-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Paswɔd Chenj -->
    <div class="col-lg-6">
        <div class="premium-card h-100">
            <div class="premium-header">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-shield-lock-fill text-warning me-2"></i>Change Password</h5>
                <small class="text-muted">Choose a strong password for your account</small>
            </div>

            <form action="{{ route('employee.settings.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-4">
                    @if(session('success_password'))
                        <div class="alert alert-success bg-opacity-10 border-success border-opacity-25 text-success alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-shield-check me-2"></i>{{ session('success_password') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Current Password</label>
                        <input type="password" name="current_password" class="form-control filter-input @error('current_password') is-invalid @enderror" placeholder="Enter current password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">New Password</label>
                        <input type="password" name="password" class="form-control filter-input @error('password') is-invalid @enderror" placeholder="Minimum 6 characters" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control filter-input" placeholder="Confirm new password" required>
                    </div>
                </div>

                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning px-4 rounded-3 fw-semibold">
                        <i class="bi bi-key-fill me-1"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
