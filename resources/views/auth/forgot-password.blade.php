<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Salon Tracker | Forgot Password</title>

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
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      crossorigin="anonymous"
    />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
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
          <p class="login-box-msg text-center text-secondary mb-3">
            Forgot your password? No problem. Enter your email and we'll send you a password reset link.
          </p>

          <!-- Session Status Message -->
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

          <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <!-- Email Input -->
            <div class="input-group mb-3">
              <input
                id="email"
                type="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="Email Address"
                value="{{ old('email') }}"
                required
                autofocus
              />
              <span class="input-group-text bg-transparent">
                <i class="bi bi-envelope text-secondary"></i>
              </span>
            </div>

            <!-- Submit Button -->
            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-primary fw-semibold py-2">
                Email Password Reset Link
              </button>
            </div>
          </form>

          <!-- Back to Login Link -->
          <div class="text-center pt-2 border-top border-secondary border-opacity-10">
            <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
              <i class="bi bi-arrow-left me-1"></i> Back to Login
            </a>
          </div>
        </div>
      </div>
    </main>

    <!-- Bootstrap 5 & AdminLTE JS -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <script src="{{ asset('js/adminlte.min.js') }}"></script>
  </body>
</html>
