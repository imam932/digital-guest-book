@extends('layouts.base')

@section('body_class', 'admin-app')

@section('content')
    <div class="admin-shell">
        <aside class="admin-sidebar-column d-none d-lg-block">
            @include('components.admin.sidebar')
        </aside>

        <div class="admin-workspace">
            @include('components.admin.topbar')

            <main class="admin-main">
                @yield('page-content')
            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="adminSidebarOffcanvas" aria-labelledby="adminSidebarOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h2 class="offcanvas-title fs-5" id="adminSidebarOffcanvasLabel">Admin menu</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('components.admin.sidebar')
        </div>
    </div>
@endsection
