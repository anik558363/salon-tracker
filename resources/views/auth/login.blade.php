<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Salon Tracker | Login</title>

    <!--begin::Theme Init-->
    <script>
        (() => {
            'use strict';
            const root = document.documentElement;

            if (root.getAttribute('data-lte-color-mode') === 'off') {
                return;
            }

            const STORAGE_KEY = 'lte-theme';
            let stored = null;
            try {
                stored = localStorage.getItem(STORAGE_KEY);
            } catch {
                // localStorage unavailable
            }
            const authored = root.getAttribute('data-bs-theme');
            let resolved = 'light';
            if (stored === 'dark' || stored === 'light') {
                resolved = stored;
            } else if (authored === 'dark' || authored === 'light') {
                resolved = authored;
            } else if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) {
                resolved = 'dark';
            }
            root.setAttribute('data-bs-theme', resolved);
            root.style.colorScheme = resolved;
            if (resolved !== authored) {
                root.setAttribute('data-lte-theme-resolved', '');
            }
        })();
    </script>
    <!--end::Theme Init-->

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        crossorigin="anonymous" />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->
</head>
<!--end::Head-->

<!--begin::Body-->

<body class="login-page bg-body-secondary d-flex align-items-center justify-content-center min-vh-100">
    <main class="login-box" style="width: 380px;">
        <div class="login-logo text-center mb-4">
            <a href="{{ url('/') }}" class="text-decoration-none text-body fw-bold fs-2">
                💈 <b>Salon</b> Tracker
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body login-card-body p-4">
                <p class="login-box-msg text-center text-secondary mb-4">Sign in to start your session</p>

                <!-- Status Messages -->
                @if (session('status'))
                    <div class="alert alert-success py-2 small mb-3 text-center" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger py-2 small mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    @if (request()->filled('email'))
                        {{-- Stylist profile theke ashle email field hide thakbe ebong hidden input jabe --}}
                        <input type="hidden" name="email" value="{{ request('email') }}" />

                        <div class="mb-3 text-center p-3 rounded-3 border border-secondary border-opacity-25"
                            style="background: rgba(212, 175, 55, 0.08);">
                            <small class="text-secondary text-uppercase d-block mb-1"
                                style="font-size: 0.72rem; letter-spacing: 1px;">Stylist Access Mode</small>
                            <span class="text-warning fw-bold fs-6">
                                <i class="bi bi-person-badge me-1"></i> {{ request('email') }}
                            </span>
                            <div class="mt-2">
                                <a href="{{ route('login') }}" class="text-decoration-none text-muted small"
                                    style="font-size: 0.78rem;">
                                    Not you? <span class="text-info">Switch account</span>
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- Shadharon login ba Admin login-er khetre normal email input field thakbe --}}
                        <div class="input-group mb-3">
                            <input id="loginEmail" type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror" placeholder="Email Address"
                                value="{{ old('email') }}" required autofocus />
                            <span class="input-group-text bg-transparent">
                                <i class="bi bi-envelope text-secondary"></i>
                            </span>
                        </div>
                    @endif

                    <!-- Password Input -->
                    <div class="input-group mb-3">
                        <input id="loginPassword" type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                            required />
                        <span class="input-group-text bg-transparent">
                            <i class="bi bi-lock-fill text-secondary"></i>
                        </span>
                    </div>

                    <!-- Remember Me & Sign In Button -->
                    <div class="row align-items-center mb-3">
                        <div class="col-7">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" />
                                <label class="form-check-label text-secondary small select-none" for="rememberMe">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-5">
                            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                                Sign In
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Forgot Password Link -->
                @if (Route::has('password.request'))
                    <div class="text-center mt-3 pt-2 border-top border-secondary border-opacity-10">
                        <a href="{{ route('password.request') }}" class="text-decoration-none small text-muted">
                            I forgot my password
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Bootstrap 5 & AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/adminlte.min.js') }}"></script>
</body>

</html>
