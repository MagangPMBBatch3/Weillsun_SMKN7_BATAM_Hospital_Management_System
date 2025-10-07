<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MedicaHub') }}</title>
    <link rel="icon" href="{{ asset('MedicaHub-Logo.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans">

    {{-- Navbar --}}
    <header class="w-full fixed top-0 left-0 z-50 bg-white/70 dark:bg-gray-900/70 backdrop-blur-md shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2 text-xl font-bold">
                <img src="{{ asset('MedicaHub-Logo.png') }}" class="w-8 h-8" alt="Logo">
                <span class="font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">{{ config('app.name', 'MedicaHub') }}</span>
            </a>

            <div class="flex gap-2">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium bg-teal-600 text-white hover:bg-teal-700">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-800">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="px-4 py-2 rounded-lg text-sm font-medium bg-teal-600 text-white hover:bg-teal-700">
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section
        class="pt-32 h-screen pb-20 bg-gradient-to-b from-teal-50 via-white to-white dark:from-gray-800 dark:via-gray-900 dark:to-gray-900 flex items-center">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <span class="text-sm uppercase tracking-wide text-teal-600 font-semibold">
                🩺 Empowering Healthcare
            </span>

            <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6">
                Simplify Your <span class="text-teal-600">Medical Management</span>
            </h1>

            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto mb-8">
                MedicaHub helps clinics and hospitals manage patients, appointments, and records efficiently in one platform.
            </p>

            <div class="flex justify-center gap-4 mb-12">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="px-6 py-3 rounded-lg bg-teal-600 text-white font-medium shadow hover:bg-teal-700 transition">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}"
                        class="px-6 py-3 rounded-lg bg-teal-600 text-white font-medium shadow hover:bg-teal-700 transition">
                        Get Started
                    </a>
                @endauth
                <a href="#features"
                    class="px-6 py-3 rounded-lg bg-gray-200 dark:bg-gray-700 font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Learn More
                </a>
            </div>

            <div class="grid grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-3xl font-bold text-teal-600">{{ $jumlahPasien ?? 0 }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">Registered Patients</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-3xl font-bold text-teal-600">{{ $jumlahDokter ?? 0 }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">Available Doctors</p>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-3xl font-bold text-teal-600">{{ $jumlahAppointment ?? 0 }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">Appointments Managed</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="py-24">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12">Powerful Features</h2>
            <div class="grid md:grid-cols-3 gap-8">

                <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold mb-2">👨‍⚕️ Patient Management</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Keep track of patient data, medical history, and ongoing treatments with ease.
                    </p>
                </div>

                <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold mb-2">📅 Appointment Scheduling</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Efficiently manage doctor-patient appointments and reduce scheduling conflicts.
                    </p>
                </div>

                <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold mb-2">📊 Analytics Dashboard</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Get insights on patient growth, visit trends, and doctor performance.
                    </p>
                </div>

                <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold mb-2">🔒 Secure Records</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Protect sensitive medical data with end-to-end encryption and secure access control.
                    </p>
                </div>

                <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold mb-2">🏥 Clinic Management</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Manage multiple clinics or departments within a single unified system.
                    </p>
                </div>

                <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold mb-2">💬 Communication Tools</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Keep your staff and patients connected through built-in messaging tools.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section id="about" class="py-20 bg-white dark:bg-gray-800">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold mb-6">Why Choose MedicaHub?</h2>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                MedicaHub helps medical teams stay organized, efficient, and connected. With its clean interface and robust
                backend, managing healthcare operations has never been easier.
            </p>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-4 bg-teal-600 text-center font-semibold tracking-wide text-sm text-white">
        &copy; {{ date('Y') }} {{ config('app.name', 'MedicaHub') }}. All rights reserved.
    </footer>

</body>

</html>
