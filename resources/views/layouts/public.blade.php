@extends('layouts.base')

@section('body_class', 'public-app')

@section('content')
    <div class="site-shell">
        @include('components.public.site-header')

        <main class="public-main">
            @yield('page-content')
        </main>

        @include('components.public.site-footer')
    </div>
@endsection
