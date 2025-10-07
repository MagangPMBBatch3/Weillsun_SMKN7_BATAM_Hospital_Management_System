<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MedicaHub') }}</title>

        <link rel="icon" href="{{ asset('MedicaHub-Logo.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-green-100 via-white to-blue-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        
        <div class="min-h-screen flex flex-col sm:justify-center items-center p-6">
            
            <!-- Logo -->
            <div class="mb-6 flex flex-col items-center">
                <x-medica-hub-logo class="w-28 h-28 drop-shadow-lg transition-transform duration-300 hover:scale-105" />
                <h1 class="mt-4 text-3xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">
                    MedicaHub
                </h1>
                <p class="text-gray-600 italic dark:text-gray-400 text-sm">Manage medical staff easily, quickly, and seamlessly</p>
            </div>

            <!-- Card -->
            <div class="w-full sm:max-w-md px-8 py-6 
                        bg-white/80 dark:bg-gray-800/80 
                        shadow-xl backdrop-blur-md
                        rounded-2xl border border-gray-200 dark:border-gray-700">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <footer class="mt-8 text-center text-xs text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} MedicaHub. All rights reserved.
            </footer>
        </div>
    </body>
</html>
