<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-text fw-bold">💈 Salon Tracker</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

                @role('super-admin')
                    <li class="nav-header text-uppercase text-muted fs-7">Management</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-speedometer2"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.employees.index') }}"
                            class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-people-fill"></i>
                            <p>Staff Management</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.services.index') }}"
                            class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-scissors"></i>
                            <p>Services & Rates</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.customers.index') }}"
                            class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-person-heart"></i>
                            <p>Clients Directory</p>
                        </a>
                    </li>

                    <li class="nav-header text-uppercase text-muted fs-7">Analytics & Reports</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.service-sales') }}"
                            class="nav-link {{ request()->routeIs('admin.reports.service-sales') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-graph-up-arrow"></i>
                            <p>Service Sales Report</p>
                        </a>
                    </li>

                    <li class="nav-header text-uppercase text-muted fs-7">Settings</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.settings.profile') }}"
                            class="nav-link {{ request()->routeIs('admin.settings.profile') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-gear-fill"></i>
                            <p>Profile & Security</p>
                        </a>
                    </li>
                @endrole

                @role('employee')
                    <li class="nav-header text-uppercase text-muted fs-7">Staff Portal</li>
                    <li class="nav-item">
                        <a href="{{ route('employee.dashboard') }}"
                            class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-house-door"></i>
                            <p>My Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('employee.entries.create') }}"
                            class="nav-link {{ request()->routeIs('employee.entries.create') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-plus-circle"></i>
                            <p>New Service Entry</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('employee.settings.profile') }}"
                            class="nav-link {{ request()->routeIs('employee.settings.profile') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-gear-fill"></i>
                            <p>My Profile & Security</p>
                        </a>
                    </li>
                @endrole

            </ul>
        </nav>
    </div>
</aside>
