<header class="public-header">
    <nav class="navbar navbar-expand-lg navbar-dark public-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <span class="brand-mark">DG</span>
                <span>Digital Guest Book</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav" aria-controls="publicNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="publicNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2 mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#guest-book">Guest Book</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light ms-lg-3" href="{{ url('/admin') }}">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
