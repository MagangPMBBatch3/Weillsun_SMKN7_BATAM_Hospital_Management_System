<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('MedicaHub-Logo.png') }}" type="image/png">

    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>

    <!-- Fonts -->
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .loader {
            --cloud-color: #4387f4;
            /* --arrows-color: #2563EB; */
            --time-animation: 1s;
            transform: scale(1.2);
        }

        .loader #cloud {
            width: 100px;
            height: 100px;
        }

        .loader #cloud rect {
            fill: var(--cloud-color);
        }

        .loader #cloud g:nth-child(3) {
            transform-origin: 50% 72.8938%;
            fill: var(--arrows-color);
            animation: rotation var(--time-animation) linear infinite;
        }

        .loader #shapes g g circle {
            animation: cloud calc(var(--time-animation) * 2) linear infinite;
        }

        .loader #shapes g g circle:nth-child(2) {
            animation-delay: calc((var(--time-animation) * 2) / -3);
        }

        .loader #shapes g g circle:nth-child(3) {
            animation-delay: calc((var(--time-animation) * 2) / -1.5);
        }

        .loader svg #lines g line {
            stroke-width: 5;
            transform-origin: 50% 50%;
            rotate: -65deg;
            animation: lines calc(var(--time-animation) / 1.33) linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            50% {
                transform: rotate(180deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes lines {
            0% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(8px);
            }
        }

        @keyframes cloud {
            0% {
                cx: 20;
                cy: 60;
                r: 15;
            }

            50% {
                cx: 50;
                cy: 45;
                r: 20;
            }

            100% {
                cx: 80;
                cy: 60;
                r: 15;
            }
        }
    </style>

</head>

<body
    class="font-sans antialiased bg-gradient-to-br from-green-100 via-white to-blue-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="min-h-screen">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header>
                <div class="max-w-8xl mx-auto pt-2 pb-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
</body>

</html>
