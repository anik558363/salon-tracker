@extends('layouts.adminlte')

@section('title', 'Salon POS Terminal')
@section('page_title', 'POS Service Billing Terminal')

@section('content')
    <style>
        .pos-container {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .pos-card {
            background: #181b20;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        .service-pos-item {
            background: #1f2329;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .service-pos-item:hover {
            transform: translateY(-3px);
            border-color: rgba(212, 175, 55, 0.4);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.45);
        }

        .service-pos-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #131518;
        }

        .service-pos-placeholder {
            width: 100%;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #131518;
            color: #f7d070;
        }

        .cart-table th {
            background: #121417 !important;
            color: #9ca3af !important;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 12px 14px;
        }

        .cart-table td {
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            padding: 12px 14px;
            vertical-align: middle;
            color: #f3f4f6;
        }

        .pos-input {
            background: #14171a !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: #f3f4f6 !important;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .pos-input:focus {
            border-color: #d4af37 !important;
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.25) !important;
        }

        .btn-qty {
            width: 28px;
            height: 28px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .pos-total-box {
            background: rgba(212, 175, 55, 0.06);
            border: 1px solid rgba(212, 175, 55, 0.25);
            border-radius: 12px;
            padding: 16px;
        }
    </style>

    <div class="pos-container">
        @if (session('success'))
            <div class="alert alert-success bg-opacity-10 border-success border-opacity-25 text-success alert-dismissible fade show mb-3"
                role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3">
            <!-- Left Side: Catalog & Search -->
            <div class="col-lg-7 col-xl-7">
                <div class="pos-card p-3 h-100 d-flex flex-column">
                    <!-- Search & Filters -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary border-opacity-25 text-muted">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="serviceSearch" class="form-control pos-input"
                                    placeholder="Search service catalog by name...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <span
                                class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 w-100 py-2 d-flex align-items-center justify-content-center">
                                <i class="bi bi-scissors me-1"></i> Available: {{ count($services) }}
                            </span>
                        </div>
                    </div>

                    <!-- Services Grid (Clean Foreach) -->
                    <div class="row g-3 overflow-auto pe-1" style="max-height: 680px;" id="servicesCatalog">
                        @if (count($services) > 0)
                            @foreach ($services as $service)
                                <div class="col-6 col-sm-4 service-card-box" data-name="{{ strtolower($service->name) }}">
                                    <div class="service-pos-item"
                                        onclick="addToCart({{ $service->id }}, '{{ addslashes($service->name) }}', {{ $service->price }})">
                                        @if (!empty($service->image))
                                            <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}"
                                                class="service-pos-img">
                                        @else
                                            <div class="service-pos-placeholder">
                                                <i class="bi bi-scissors fs-1"></i>
                                            </div>
                                        @endif
                                        <div class="p-2 flex-grow-1 d-flex flex-column justify-content-between">
                                            <div class="fw-bold text-white small mb-1 lh-sm">{{ $service->name }}</div>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span class="text-success fw-bold font-monospace small">AED
                                                    {{ number_format($service->price, 2) }}</span>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-warning py-0 px-2 rounded-pill"
                                                    style="font-size: 0.75rem;">
                                                    <i class="bi bi-plus-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i> No active services available.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side: POS Bill & Cart Details -->
            <div class="col-lg-5 col-xl-5">
                <div class="pos-card p-3">
                    <form action="{{ route('employee.entries.store') }}" method="POST" id="posBillingForm">
                        @csrf

                        <!-- Stylist & Date Bar -->
                        <div
                            class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-secondary border-opacity-10">
                            <div>
                                <span class="text-muted small d-block">Operator Stylist</span>
                                <span class="text-warning fw-bold"><i class="bi bi-person-badge me-1"></i>
                                    {{ auth()->user()->name }}</span>
                            </div>
                            <div class="text-end">
                                <span class="text-muted small d-block">Invoice Date</span>
                                <span class="text-white small font-monospace">{{ date('d M, Y') }}</span>
                            </div>
                        </div>

                        <!-- Client Information Fields -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted small fw-semibold mb-1">Client Name</label>
                                <input type="text" name="customer_name" class="form-control pos-input"
                                    placeholder="e.g. Sheikh Rashid">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small fw-semibold mb-1">Phone (Optional)</label>
                                <input type="text" name="customer_phone" class="form-control pos-input"
                                    placeholder="+971 50 xxx xxxx">
                            </div>
                        </div>

                        <!-- Cart Table -->
                        <div class="table-responsive mb-3" style="min-height: 220px; max-height: 280px; overflow-y: auto;">
                            <table class="table cart-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center" style="width: 70px;">Rate</th>
                                        <th class="text-center" style="width: 90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody">
                                    <tr id="emptyCartRow">
                                        <td colspan="3" class="text-center py-5 text-muted small">
                                            <i class="bi bi-cart-x fs-2 d-block mb-1 text-secondary"></i>
                                            Click on services from the left catalog to add to invoice.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Hidden Inputs for Form Submission -->
                        <div id="hiddenServicesContainer"></div>

                        <!-- Summary & Calculation -->
                        <div class="pos-total-box mb-3">
                            <div class="d-flex justify-content-between text-muted small mb-2">
                                <span>Total Items:</span>
                                <span id="posItemCount" class="text-white font-monospace">0</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mb-2">
                                <span>Subtotal:</span>
                                <span id="posSubtotal" class="text-white font-monospace">AED 0.00</span>
                            </div>
                            <div
                                class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                                <span class="text-white fw-bold fs-6">Grand Total:</span>
                                <span id="posGrandTotal" class="text-success fw-bold fs-4 font-monospace">AED 0.00</span>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-3">
                            <textarea name="note" class="form-control pos-input" rows="2"
                                placeholder="Notes / Styling notes / Client preferences..."></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-danger w-50 py-2 rounded-3"
                                onclick="clearCart()">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary w-50 py-2 rounded-3 fw-bold" id="checkoutBtn"
                                disabled>
                                <i class="bi bi-printer-fill me-1"></i> Complete & Pay
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let cart = [];

        function addToCart(id, name, price) {
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({
                    id,
                    name,
                    price,
                    qty: 1
                });
            }
            renderCart();
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            renderCart();
        }

        function clearCart() {
            cart = [];
            renderCart();
        }

        function renderCart() {
            const tbody = document.getElementById('cartTableBody');
            const hiddenContainer = document.getElementById('hiddenServicesContainer');
            const itemCount = document.getElementById('posItemCount');
            const subtotalEl = document.getElementById('posSubtotal');
            const grandTotalEl = document.getElementById('posGrandTotal');
            const checkoutBtn = document.getElementById('checkoutBtn');

            tbody.innerHTML = '';
            hiddenContainer.innerHTML = '';

            if (cart.length === 0) {
                tbody.innerHTML = `
                <tr id="emptyCartRow">
                    <td colspan="3" class="text-center py-5 text-muted small">
                        <i class="bi bi-cart-x fs-2 d-block mb-1 text-secondary"></i>
                        Click on services from the left catalog to add to invoice.
                    </td>
                </tr>`;
                itemCount.textContent = '0';
                subtotalEl.textContent = 'AED 0.00';
                grandTotalEl.textContent = 'AED 0.00';
                checkoutBtn.disabled = true;
                return;
            }

            let total = 0;
            let totalItems = 0;

            cart.forEach(item => {
                total += item.price * item.qty;
                totalItems += item.qty;

                for (let i = 0; i < item.qty; i++) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'services[]';
                    input.value = item.id;
                    hiddenContainer.appendChild(input);
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>
                    <div class="fw-semibold text-white small">${item.name}</div>
                    <span class="text-muted font-monospace" style="font-size: 0.75rem;">AED ${item.price.toFixed(2)}</span>
                </td>
                <td class="text-center font-monospace text-success fw-bold small">
                    AED ${(item.price * item.qty).toFixed(2)}
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-qty" onclick="removeFromCart(${item.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
                tbody.appendChild(tr);
            });

            itemCount.textContent = totalItems;
            subtotalEl.textContent = 'AED ' + total.toFixed(2);
            grandTotalEl.textContent = 'AED ' + total.toFixed(2);
            checkoutBtn.disabled = false;
        }

        document.getElementById('serviceSearch').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.service-card-box').forEach(card => {
                const name = card.getAttribute('data-name');
                if (name.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
@endpush
