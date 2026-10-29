<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <!-- Fonts (Prompt Font สำหรับภาษาไทยที่ทันสมัยและอ่านง่าย) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Prompt', sans-serif;
            }
        </style>

        <!-- Stylesheets (Pure CSS via public/css) -->
        <link rel="stylesheet" href="{{ asset('css/layout_css.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

        <!-- Scripts -->
        @vite(['resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-slate-50">
        <div class="app-wrapper min-h-screen flex flex-col justify-between">
            <div>
                @include('layouts.navigation')

                <!-- Page Heading -->
                @if (View::hasSection('header'))
                    <header class="app-header page-header bg-white border-b border-slate-200">
                        <div class="app-header-inner max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                            @yield('header')
                        </div>
                    </header>
                @elseif (isset($header))
                    <header class="app-header page-header bg-white border-b border-slate-200">
                        <div class="app-header-inner max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="app-main page-content max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <x-flash-messages />
                    @yield('content')
                    {{ $slot ?? '' }}
                </main>
            </div>

            <!-- Footer -->
            <footer class="app-footer page-footer bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
                ระบบจัดการและยืม-คืนครุภัณฑ์องค์กร &bull; วิชา 954348 Term Project &bull; พัฒนาด้วย Laravel & Blade
            </footer>
        </div>
    </body>
</html>
