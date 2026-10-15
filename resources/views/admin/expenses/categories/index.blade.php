@extends('layouts.adminlte')

@section('title', 'Expense Categories')
@section('page_title', 'Expense Categories Directory')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success bg-opacity-10 border-success border-opacity-25 text-success alert-dismissible fade show mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="premium-card mb-4">
            <!-- Filter & Action Header -->
            <div class="p-4 border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <form action="{{ route('admin.expense-categories.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control filter-input" placeholder="Search category..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary px-3 rounded-3">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.expense-categories.index') }}" class="btn btn-outline-secondary px-3 rounded-3">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                        <i class="bi bi-wallet2 me-1"></i> Expenses List
                    </a>
                    <a href="{{ route('admin.expense-categories.create') }}" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Add Category
                    </a>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="table-responsive">
                <table class="table premium-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th class="text-center">Total Entries</th>
                            <th>Created On</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">
                                {{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}
                            </td>
                            <td>
                                <div class="fw-bold text-light fs-6">{{ $category->name }}</div>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $category->description ?? 'No description provided.' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 px-2 py-1">
                                    {{ $category->expenses_count }}
                                </span>
                            </td>
                            <td class="text-muted small font-monospace">{{ $category->created_at->format('d M, Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.expense-categories.edit', $category->id) }}" class="btn btn-sm btn-outline-info rounded-3 me-2" title="Edit Category">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('admin.expense-categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deleting category will also remove all its linked expenses. Proceed?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Delete Category">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary"></i> No expense categories found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-end">
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
