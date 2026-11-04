<div class="admin-sidebar d-flex flex-column h-100 p-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <span class="brand-mark">DG</span>
        <div>
            <div class="fw-semibold text-dark">Guest Book</div>
            <div class="small text-muted">Admin portal</div>
        </div>
    </div>

    <nav class="nav nav-pills flex-column gap-2 mt-3">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a class="nav-link {{ request()->routeIs('admin.guest-books.*') ? 'active' : '' }}" href="{{ route('admin.guest-books.index') }}">Guest Book Records</a>
        <a class="nav-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}" href="{{ route('admin.companies.index') }}">Companies</a>
        <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">Event Settings</a>
    </nav>

    <div class="mt-auto pt-4 border-top">
        <div class="small text-muted mb-2">Current event</div>
        <div class="fw-semibold">Annual Leadership Summit</div>
    </div>
</div>
