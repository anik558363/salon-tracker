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
                        <a href="{{ route('admin.reports.financial-ledger') }}"
                            class="nav-link {{ request()->routeIs('admin.reports.financial-ledger') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-journal-bookmark-fill text-warning"></i>
                            <p>Master Ledger</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.reports.service-sales') }}"
                            class="nav-link {{ request()->routeIs('admin.reports.service-sales') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-graph-up-arrow"></i>
                            <p>Service Sales Report</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.employee') }}"
                            class="nav-link {{ request()->routeIs('admin.reports.employee') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-person-lines-fill"></i>
                            <p>Employee Report</p>
                        </a>
                    </li>

                    <li class="nav-header text-uppercase text-secondary small fw-bold px-3 pt-3">Finance & Expenses</li>

                    <li class="nav-item">
                        <a href="{{ route('admin.expenses.index') }}"
                            class="nav-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-wallet2"></i>
                            <p>Salon Expenses</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.expense-categories.index') }}"
                            class="nav-link {{ request()->routeIs('admin.expense-categories.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-tags"></i>
                            <p>Expense Categories</p>
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

                @if (auth()->user()->hasRole('employee'))
                    <li class="nav-item">
                        <a href="{{ route('employee.entries.create') }}"
                            class="nav-link {{ request()->routeIs('employee.entries.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-scissors"></i>
                            <p>New Service Entry</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('employee.settings.profile') }}"
                            class="nav-link {{ request()->routeIs('employee.settings.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-person-gear"></i>
                            <p>Profile & Security</p>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
