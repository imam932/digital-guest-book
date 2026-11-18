@extends('layouts.base')

@section('body_class', 'public-app')

@section('content')
    <div class="site-shell">
        @include('components.public.site-header')

        <main class="public-main">
            @yield('page-content')
        </main>

        @unless (request()->routeIs('message-wall', 'guest-book.form'))
            @include('components.public.site-footer')
        @endunless
    </div>
@endsection
