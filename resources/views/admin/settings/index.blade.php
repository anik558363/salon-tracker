@extends('layouts.adminlte')

@section('title', 'Salon Settings')
@section('page_title', 'General Settings & Branding')

@section('content')
<div class="row">
    <div class="col-12 col-xl-10 mx-auto">
        @if(session('success'))
            <div class="alert alert-success bg-opacity-10 border-success border-opacity-25 text-success alert-dismissible fade show mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="premium-card p-4 mb-4">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Basic Branding -->
                <h5 class="text-white fw-bold mb-3"><i class="bi bi-shop text-warning me-2"></i>Salon Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Salon Business Name *</label>
                        <input type="text" name="salon_name" class="form-control filter-input"
                               value="{{ setting('salon_name', 'LUXE SALON') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">City / Branch Title</label>
                        <input type="text" name="salon_city" class="form-control filter-input"
                               value="{{ setting('salon_city', 'DUBAI') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-semibold">Currency Code</label>
                        <input type="text" name="currency" class="form-control filter-input"
                               value="{{ setting('currency', 'AED') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-semibold">Currency Symbol</label>
                        <input type="text" name="currency_symbol" class="form-control filter-input"
                               value="{{ setting('currency_symbol', 'د.إ') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-semibold">Phone Number</label>
                        <input type="text" name="salon_phone" class="form-control filter-input"
                               value="{{ setting('salon_phone', '+971 4 000 0000') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-semibold">Email Address</label>
                        <input type="email" name="salon_email" class="form-control filter-input"
                               value="{{ setting('salon_email', 'info@luxesalon.com') }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label text-muted small fw-semibold">Salon Physical Address</label>
                        <textarea name="salon_address" class="form-control filter-input" rows="2">{{ setting('salon_address', 'Downtown Boulevard, Dubai, United Arab Emirates') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label text-muted small fw-semibold">Google Maps URL</label>
                        <input type="text" name="google_maps_url" class="form-control filter-input"
                               value="{{ setting('google_maps_url', 'https://maps.google.com') }}">
                    </div>
                </div>

                <hr class="border-secondary border-opacity-25 my-4">

                <!-- Operating Hours -->
                <h5 class="text-white fw-bold mb-3"><i class="bi bi-clock-history text-info me-2"></i>Working Schedule</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">Saturday – Thursday Hours</label>
                        <input type="text" name="hours_sat_thu" class="form-control filter-input"
                               value="{{ setting('hours_sat_thu', '10:00 AM – 10:00 PM') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">Friday Hours</label>
                        <input type="text" name="hours_friday" class="form-control filter-input"
                               value="{{ setting('hours_friday', '01:30 PM – 11:00 PM') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">Walk-in Policy Text</label>
                        <input type="text" name="walkin_policy" class="form-control filter-input"
                               value="{{ setting('walkin_policy', 'Always Welcome') }}">
                    </div>
                </div>

                <hr class="border-secondary border-opacity-25 my-4">

                <!-- Landing Page Hero & Footer Content -->
                <h5 class="text-white fw-bold mb-3"><i class="bi bi-window text-success me-2"></i>Landing Page Content</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-semibold">Hero Top Badge</label>
                        <input type="text" name="hero_badge" class="form-control filter-input"
                               value="{{ setting('hero_badge', 'Premium Grooming & Styling in Dubai') }}">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label text-muted small fw-semibold">Hero Main Title</label>
                        <input type="text" name="hero_title" class="form-control filter-input"
                               value="{{ setting('hero_title', 'Redefining Elegance, Luxury & Precision Grooming') }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label text-muted small fw-semibold">Hero Subtitle / Description</label>
                        <textarea name="hero_subtitle" class="form-control filter-input" rows="2">{{ setting('hero_subtitle', 'Experience bespoke hair treatments, executive styling, and rejuvenation in the heart of the United Arab Emirates.') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label text-muted small fw-semibold">Footer Subtext</label>
                        <input type="text" name="footer_text" class="form-control filter-input"
                               value="{{ setting('footer_text', 'Designed for excellence and bespoke hospitality.') }}">
                    </div>
                </div>

                <hr class="border-secondary border-opacity-25 my-4">

                <!-- Logos & Visual Assets -->
                <h5 class="text-white fw-bold mb-3"><i class="bi bi-image text-warning me-2"></i>Logo & Favicon</h5>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Main Logo</label>
                        <div class="d-flex align-items-center gap-3">
                            @if(setting('logo'))
                                <img src="{{ asset('storage/' . setting('logo')) }}" alt="Logo" class="rounded-3 border border-secondary p-1" style="max-height: 55px; max-width: 120px; background: #14171a;">
                            @else
                                <div class="rounded-3 border border-secondary d-flex align-items-center justify-content-center text-muted" style="width: 55px; height: 55px; background: #14171a;">
                                    <i class="bi bi-scissors fs-4 text-warning"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <input type="file" name="logo" class="form-control filter-input">
                                <small class="text-secondary d-block mt-1">PNG / SVG / WebP transparent recommended.</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-semibold">Browser Favicon</label>
                        <div class="d-flex align-items-center gap-3">
                            @if(setting('favicon'))
                                <img src="{{ asset('storage/' . setting('favicon')) }}" alt="Favicon" class="rounded border border-secondary p-1" style="width: 40px; height: 40px;">
                            @else
                                <div class="rounded border border-secondary d-flex align-items-center justify-content-center text-muted" style="width: 40px; height: 40px; background: #14171a;">
                                    <i class="bi bi-globe fs-5"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <input type="file" name="favicon" class="form-control filter-input">
                                <small class="text-secondary d-block mt-1">16x16 or 32x32 PNG / ICO.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow">
                        <i class="bi bi-save me-1"></i> Save All Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
