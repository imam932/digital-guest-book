<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid px-4">
        <span class="navbar-brand mb-0 h1 d-none d-lg-inline">Welcome back</span>

        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="badge bg-success-subtle text-success border border-success-subtle">Live</span>

            <div class="d-flex align-items-center gap-2 me-3">
                <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                <div class="d-flex flex-column">
                    <span class="fw-semibold">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <small class="text-muted">Event manager</small>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>
