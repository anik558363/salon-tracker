<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Luxe Salon & Lounge | Dubai, UAE</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=fallback" rel="stylesheet">

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg-dark: #0f1115;
            --bg-card: #181b20;
            --bg-card-hover: #1f2329;
            --gold-accent: #d4af37;
            --gold-gradient: linear-gradient(135deg, #f7d070 0%, #c59b27 100%);
            --text-light: #f9fafb;
            --text-sub: #d1d5db;
            --text-muted-custom: #9ca3af;
            --border-glow: rgba(212, 175, 55, 0.25);
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        /* Readability & Text Contrast */
        .hero-subtitle {
            color: var(--text-sub) !important;
            font-size: 1.15rem;
            line-height: 1.7;
        }

        .card-description {
            color: #9ca3af !important;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .service-subtext {
            color: #9ca3af !important;
            font-size: 0.85rem;
        }

        .timing-label {
            color: #d1d5db !important;
        }

        .location-details {
            color: #d1d5db !important;
        }

        /* Navbar */
        .luxury-nav {
            background: rgba(15, 17, 21, 0.9);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 18px 0;
        }

        .gold-text {
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-gold {
            background: var(--gold-gradient);
            color: #0b0c0e;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            padding: 11px 26px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 18px rgba(197, 155, 39, 0.28);
        }

        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(197, 155, 39, 0.45);
            color: #000;
        }

        .btn-outline-gold {
            border: 1px solid rgba(212, 175, 55, 0.6);
            color: #f7d070;
            border-radius: 8px;
            padding: 11px 26px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: transparent;
        }

        .btn-outline-gold:hover {
            background: rgba(212, 175, 55, 0.12);
            color: #fff;
            border-color: #d4af37;
        }

        /* Hero */
        .hero-section {
            padding: 130px 0 90px;
            position: relative;
            background: radial-gradient(circle at 50% 25%, rgba(212, 175, 55, 0.12), transparent 65%);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 18px;
            border-radius: 30px;
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid var(--border-glow);
            color: #f7d070;
            font-size: 0.85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 24px;
        }

        /* Features */
        .feature-box {
            background: var(--bg-card);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 32px;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-box:hover {
            transform: translateY(-4px);
            border-color: var(--border-glow);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45);
        }

        .feature-icon {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            background: rgba(212, 175, 55, 0.12);
            color: #f7d070;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 20px;
            border: 1px solid var(--border-glow);
        }

        /* Services */
        .rate-card-item {
            background: var(--bg-card);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            padding: 20px 24px;
            transition: all 0.25s ease;
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .rate-card-item:hover {
            background: var(--bg-card-hover);
            border-color: rgba(212, 175, 55, 0.35);
        }

        .price-tag {
            font-size: 1.15rem;
            font-weight: 700;
            color: #34d399;
            background: rgba(16, 185, 129, 0.12);
            padding: 6px 16px;
            border-radius: 8px;
            border: 1px solid rgba(52, 211, 153, 0.28);
            white-space: nowrap;
        }

        footer {
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            background: #0b0d0f;
            padding: 40px 0 25px;
        }
    </style>
</head>
<body>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg luxury-nav sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center text-white fw-bold fs-4" href="{{ url('/') }}">
                <span class="me-2 fs-3">💈</span> <span class="gold-text">LUXE SALON</span>&nbsp;<span class="text-white fw-light fs-6">DUBAI</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <i class="bi bi-list text-white fs-2"></i>
            </button>

            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link text-white-50 px-3" href="#services">Services & Rates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50 px-3" href="#about">Why Choose Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50 px-3" href="#contact">Location & Hours</a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        @auth
                            @if(auth()->user()->hasRole('super-admin'))
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-gold btn-sm px-4">
                                    <i class="bi bi-speedometer2 me-1"></i> Admin Portal
                                </a>
                            @else
                                <a href="{{ route('employee.dashboard') }}" class="btn btn-gold btn-sm px-4">
                                    <i class="bi bi-person-workspace me-1"></i> Staff Portal
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-gold btn-sm px-4">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Staff Login
                            </a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="hero-badge">
                        <i class="bi bi-stars"></i> Premium Grooming & Styling in Dubai
                    </div>
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Redefining Elegance, Luxury & Precision Grooming
                    </h1>
                    <p class="hero-subtitle mb-5 px-lg-5">
                        Experience bespoke hair treatments, executive styling, and rejuvenation in the heart of the United Arab Emirates. Dedicated to perfection and timeless sophistication.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="#services" class="btn btn-gold px-4 py-3 fs-6">
                            <i class="bi bi-scissors me-2"></i> View Rate Card
                        </a>
                        <a href="#contact" class="btn btn-outline-gold px-4 py-3 fs-6">
                            <i class="bi bi-geo-alt me-2"></i> Visit Our Lounge
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features / Highlights -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon"><i class="bi bi-award"></i></div>
                        <h4 class="text-white fw-bold mb-3">Master Stylists</h4>
                        <p class="card-description mb-0">World-class barbers and aestheticians trained to deliver elite grooming tailored to your distinct persona.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                        <h4 class="text-white fw-bold mb-3">Private VIP Suites</h4>
                        <p class="card-description mb-0">Immerse yourself in complete privacy and tranquility while enjoying premium beverages and tailored care.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon"><i class="bi bi-droplet-half"></i></div>
                        <h4 class="text-white fw-bold mb-3">Organic Luxury Products</h4>
                        <p class="card-description mb-0">We use only top-tier imported, dermatologically tested, organic hair and skincare formulations.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services & Pricing List -->
    <section id="services" class="py-5" style="background: rgba(255, 255, 255, 0.015);">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-uppercase gold-text fw-bold small tracking-wider">Our Menu</span>
                <h2 class="display-6  fw-bold text-white mt-1">Signature Services & Rates</h2>
                <p class="card-description">Transparent pricing curated for executive grooming</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    @forelse($services as $service)
                        <div class="rate-card-item">
                            <div>
                                <h5 class="text-white mb-1 fw-bold">{{ $service->name }}</h5>
                                <div class="service-subtext">Professional salon service with premium finish</div>
                            </div>
                            <div>
                                <span class="price-tag">AED {{ number_format($service->price, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-scissors fs-1 d-block mb-3 text-secondary"></i>
                            <p>Our updated catalog will be available shortly.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- Contact & Operating Hours -->
    <section id="contact" class="py-5">
        <div class="container py-4">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-5">
                    <div class="feature-box">
                        <h4 class="font-serif fw-bold text-white mb-3"><i class="bi bi-clock-history gold-text me-2"></i>Operating Hours</h4>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-10">
                                <span class="timing-label">Saturday – Thursday:</span>
                                <span class="text-white fw-semibold">10:00 AM – 10:00 PM</span>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-10">
                                <span class="timing-label">Friday:</span>
                                <span class="text-white fw-semibold">01:30 PM – 11:00 PM</span>
                            </li>
                            <li class="d-flex justify-content-between py-2 pt-3">
                                <span class="timing-label">Walk-in Policy:</span>
                                <span class="text-success fw-semibold">Always Welcome</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="feature-box">
                        <h4 class="font-serif fw-bold text-white mb-3"><i class="bi bi-geo-alt-fill gold-text me-2"></i>Salon Location</h4>
                        <p class="location-details mb-2">Downtown Boulevard, Dubai, United Arab Emirates</p>
                        <p class="location-details mb-3"><i class="bi bi-telephone gold-text me-2"></i>+971 4 000 0000</p>
                        <a href="https://maps.google.com" target="_blank" class="btn btn-outline-gold btn-sm">
                            <i class="bi bi-map me-1"></i> Open in Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-2 text-white-50">
                &copy; {{ date('Y') }} <strong>Luxe Salon & Lounge</strong>. All rights reserved. Dubai, UAE.
            </p>
            <small class="text-secondary">
                Designed for excellence and bespoke hospitality.
            </small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
