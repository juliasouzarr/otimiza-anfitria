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
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-brand to-brand-accent">
            <div class="mb-3 flex justify-center">
                <a href="/">
                    <img src="{{ asset('images/logobranca.png') }}" class="drop-shadow-md hover:scale-105 transition-transform duration-300" style="max-height: 85px; width: auto;" alt="Otimiza Anfitriã" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-10 py-10 bg-white shadow-2xl overflow-hidden sm:rounded-3xl border border-gray-100">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-white text-sm opacity-80">
                &copy; {{ date('Y') }} Otimiza Anfitriã. Todos os direitos reservados.
            </div>
        </div>
    </body>
</html>
