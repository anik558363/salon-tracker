@extends('layouts.adminlte')

@section('title', 'New Service Entry')
@section('page_title', 'New Service Entry')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Client & Service Details</h5>
            </div>
            <form action="{{ route('employee.entries.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <!-- Client Details -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Client Name (Optional)</label>
                            <input type="text" name="customer_name" class="form-control" placeholder="e.g. John Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number (Optional)</label>
                            <input type="text" name="customer_phone" class="form-control" placeholder="+971 50 xxx xxxx">
                        </div>
                    </div>

                    <!-- Services Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Services:</label>
                        <div class="row g-2">
                            @foreach($services as $service)
                            <div class="col-sm-6">
                                <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input service-checkbox" type="checkbox" name="services[]" value="{{ $service->id }}" data-price="{{ $service->price }}" id="srv_{{ $service->id }}">
                                        <label class="form-check-label ms-1" for="srv_{{ $service->id }}">
                                            {{ $service->name }}
                                        </label>
                                    </div>
                                    <span class="badge bg-secondary">AED {{ number_format($service->price, 2) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('services')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Live Total Bill Preview -->
                    <div class="alert alert-info d-flex justify-content-between align-items-center my-3">
                        <span class="fs-6 fw-bold">Total Bill:</span>
                        <span id="total-price" class="fs-4 fw-bold text-primary">AED 0.00</span>
                    </div>

                    <!-- Note -->
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Any specific requirements or comments..."></textarea>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check2-circle me-1"></i> Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const checkboxes = document.querySelectorAll('.service-checkbox');
    const totalPriceEl = document.getElementById('total-price');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            let total = 0;
            checkboxes.forEach(c => {
                if(c.checked) total += parseFloat(c.dataset.price);
            });
            totalPriceEl.textContent = 'AED ' + total.toFixed(2);
        });
    });
</script>
@endpush
