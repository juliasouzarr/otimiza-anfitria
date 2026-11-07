<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Otimiza Anfitriã') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="256x256" href="{{ asset('images/favicon.png') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-slate-50">
        <div x-data="{ 
            sidebarOpen: false, 
            sidebarCollapsed: JSON.parse(localStorage.getItem('sidebar_collapsed') || 'false') 
        }" 
        x-init="$watch('sidebarCollapsed', val => localStorage.setItem('sidebar_collapsed', JSON.stringify(val)))"
        class="min-h-screen flex flex-col md:flex-row bg-slate-50">
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
                <!-- Page Heading (Header Bar) -->
                @isset($header)
                    <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-slate-200/70 sticky top-0 z-20">
                        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
