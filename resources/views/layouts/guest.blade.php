<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') - @endif{{ config('app.name', 'ระบบจัดการครุภัณฑ์') }}</title>

        <!-- Fonts (Prompt Font) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Stylesheets (Pure CSS via public/css) -->
        <link rel="stylesheet" href="{{ asset('css/layout_css.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

        <!-- Scripts -->
        @vite(['resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="guest-container min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <h1 class="guest-title text-xl font-bold text-slate-800 text-center">
                    @yield('title')
                </h1>
            </div>

            <div class="guest-card w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                @yield('content')
                {{ $slot ?? '' }}
            </div>
        </div>
    </body>
</html>
