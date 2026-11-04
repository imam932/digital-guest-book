@extends('layouts.base')

@section('body_class', 'admin-app')

@section('content')
    <div class="admin-shell">
        <div class="container-fluid p-0">
            <div class="row g-0 min-vh-100">
                <aside class="col-lg-2 border-end bg-white">
                    @include('components.admin.sidebar')
                </aside>

                <div class="col-lg-10">
                    @include('components.admin.topbar')

                    <main class="admin-main">
                        @yield('page-content')
                    </main>
                </div>
            </div>
        </div>
    </div>
@endsection
