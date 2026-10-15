@extends('layouts.adminlte')

@section('title', 'Add Expense Category')
@section('page_title', 'New Expense Category')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="premium-card">
            <div class="premium-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-tag-fill text-primary me-2"></i>Create New Category</h5>
                <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Categories
                </a>
            </div>

            <form action="{{ route('admin.expense-categories.store') }}" method="POST">
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

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Category Name</label>
                        <input type="text" name="name" class="form-control filter-input" value="{{ old('name') }}" placeholder="e.g. Utility Bills, Products & Dyes, Rent" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Description (Optional)</label>
                        <textarea name="description" rows="4" class="form-control filter-input" placeholder="Short description about this category...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-outline-secondary rounded-3">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
