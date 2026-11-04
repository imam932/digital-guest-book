<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Digital Guest Book')</title>
        <meta name="description" content="@yield('meta_description', 'Interactive guest book for corporate events.')">

        @vite([
            'resources/css/app.css',
            'resources/css/public.css',
            'resources/css/admin.css',
            'resources/js/app.js',
            'resources/js/public.js',
            'resources/js/admin.js',
        ])
    </head>
    <body class="@yield('body_class', '')">
        @yield('content')
    </body>
</html>
