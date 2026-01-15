<x-app-layout>
    <x-slot name="header">
        <div class="relative rounded-xl overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-blue-500 to-purple-600 opacity-90"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/20 rounded-full -ml-24 -mb-24 blur-2xl"></div>

            <!-- Content -->
            <div class="relative px-6 py-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Icon -->
                        <div
                            class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg border border-white/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>

                        <!-- Title -->
                        <div>
                            <h2 class="font-bold text-4xl text-white drop-shadow-lg leading-tight">
                                {{ __('Dashboard') }}
                            </h2>
                            <p class="text-blue-100 text-sm mt-1 font-medium">Welcome back! Here's your overview</p>
                        </div>
                    </div>

                    <!-- Date & Time Info -->
                    <div
                        class="hidden md:flex items-center gap-3 bg-white/20 backdrop-blur-sm px-5 py-3 rounded-2xl border border-white/30 shadow-lg">
                        <div class="text-right">
                            <p class="text-white font-semibold text-lg font-mono" id="current-time">12:00:00 PM</p>
                            <p class="text-blue-100 text-xs font-medium" id="current-date">Monday, Jan 12, 2026</p>
                        </div>
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <script>
        // Update time and date with seconds
        function updateDateTime() {
            const now = new Date();

            // Update time with seconds
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                const hours = now.getHours();
                const minutes = now.getMinutes();
                const seconds = now.getSeconds();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                const displayHours = hours % 12 || 12;
                const displayMinutes = minutes < 10 ? '0' + minutes : minutes;
                const displaySeconds = seconds < 10 ? '0' + seconds : seconds;
                timeElement.textContent = `${displayHours}:${displayMinutes}:${displaySeconds} ${ampm}`;
            }

            // Update date
            const dateElement = document.getElementById('current-date');
            if (dateElement) {
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                };
                dateElement.textContent = now.toLocaleDateString('en-US', options);
            }
        }

        // Update immediately and then every second
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>

    <style>
        /* Subtle animation for header */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>

    <div class="px-6">
        {{-- Admin Dashboard --}}
        @if (auth()->user()->role === 'admin')
            <!-- Top Stats Row 1 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Patients Card -->
                <div
                    class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-blue-200 dark:border-blue-700 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-blue-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-6 w-7 h-7 text-white">
                                    <path
                                        d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z" />
                                </svg>


                            </div>
                            <div class="px-3 py-1 bg-blue-500/20 rounded-full">
                                <span
                                    class="text-xs font-semibold text-blue-700 dark:text-blue-300">+{{ $newPatientsMonth ?? 0 }}</span>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-2">
                            Total Patients</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">{{ $totalPatients ?? 0 }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">New patients this month</p>
                    </div>
                </div>

                <!-- Total Medical Staff Card -->
                <div
                    class="group bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/30 dark:to-emerald-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-green-200 dark:border-green-700 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-green-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-green-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="flex items-center gap-1">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span
                                    class="text-xs font-semibold text-green-700 dark:text-green-300">{{ $doctorsOnDutyCount ?? 0 }}
                                    active</span>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400 uppercase tracking-wide mb-2">
                            Medical Staff</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">{{ $totalMedicalStaff ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Currently on duty</p>
                    </div>
                </div>

                <!-- Today's Visits Card -->
                <div
                    class="group bg-gradient-to-br from-purple-50 to-violet-100 dark:from-purple-900/30 dark:to-violet-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-purple-200 dark:border-purple-700 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-purple-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-purple-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p
                            class="text-sm font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-2">
                            Today's Visits</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">{{ $todayVisits ?? 0 }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $monthlyVisits ?? 0 }} visits this month
                        </p>
                    </div>
                </div>

                <!-- Total Revenue Card -->
                <div
                    class="group bg-gradient-to-br from-orange-50 to-amber-100 dark:from-orange-900/30 dark:to-amber-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-orange-200 dark:border-orange-700 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-orange-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-orange-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p
                            class="text-sm font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wide mb-2">
                            Today's Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Rp
                            {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Rp
                            {{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }} monthly</p>
                    </div>
                </div>
            </div>

            <!-- Top Stats Row 2 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Inpatient Care -->
                <div
                    class="group bg-gradient-to-br from-red-50 to-rose-100 dark:from-red-900/30 dark:to-rose-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-red-200 dark:border-red-700 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-red-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-red-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-red-600 dark:text-red-400 uppercase tracking-wide mb-2">
                            Inpatient</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">{{ $inpatientCount ?? 0 }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Occupied beds</p>
                    </div>
                </div>

                <!-- Pending Payments -->
                <div
                    class="group bg-gradient-to-br from-yellow-50 to-amber-100 dark:from-yellow-900/30 dark:to-amber-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-yellow-200 dark:border-yellow-700 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-yellow-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p
                            class="text-sm font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wide mb-2">
                            Patient Payments</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ $pendingPaymentsCount ?? 0 }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Rp
                            {{ number_format($pendingPaymentsAmount ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Medicine Stock -->
                <div
                    class="group bg-gradient-to-br from-indigo-50 to-blue-100 dark:from-indigo-900/30 dark:to-blue-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-indigo-200 dark:border-indigo-700 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-indigo-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            @if (($lowStockAlert ?? 0) > 0)
                                <div class="px-2 py-1 bg-red-500/20 rounded-full flex items-center gap-1">
                                    <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                                    <span
                                        class="text-xs font-semibold text-red-700 dark:text-red-300">{{ $lowStockAlert }}</span>
                                </div>
                            @endif
                        </div>
                        <p
                            class="text-sm font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wide mb-2">
                            Medicine</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">{{ $medicineTypes ?? 0 }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $lowStockAlert ?? 0 }} items low stock
                        </p>
                    </div>
                </div>

                <!-- Active Clinics -->
                <div
                    class="group bg-gradient-to-br from-cyan-50 to-teal-100 dark:from-cyan-900/30 dark:to-teal-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-cyan-200 dark:border-cyan-700 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-cyan-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-cyan-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m0 0h-.581m0 0H5m0 0h2m0 0h2">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-cyan-600 dark:text-cyan-400 uppercase tracking-wide mb-2">
                            Clinics</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">{{ $clinicCount ?? 0 }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $activeClinicsToday ?? 0 }} active
                            today</p>
                    </div>
                </div>
            </div>

            <!-- Main Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Doctors on Duty Today -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-xl">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Doctors on Duty Today</h3>
                    </div>
                    <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                        @forelse($doctorsOnDuty ?? [] as $doctor)
                            <div
                                class="group flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-lg group-hover:scale-110 transition-transform duration-300">
                                        {{ substr($doctor['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $doctor['name'] }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $doctor['specialization'] ?? 'General' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span
                                        class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">On
                                        Duty</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">No doctors scheduled for today</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Visits -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Recent Visits</h3>
                    </div>
                    <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                        @forelse($recentVisits ?? [] as $visit)
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 hover:shadow-md transition-all duration-300">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-bold">
                                            {{ substr($visit['patient_name'], 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900 dark:text-white mb-1">
                                                {{ $visit['patient_name'] }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $visit['clinic'] ?? 'General Clinic' }}</p>
                                        </div>
                                    </div>
                                    <span
                                        class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-full whitespace-nowrap">{{ $visit['date'] ?? 'Today' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">No recent visits</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Bottom Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Top Clinics -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top Clinics</h3>
                    </div>
                    <div class="space-y-3">
                        @forelse($topClinics ?? [] as $index => $clinic)
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors duration-300">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $clinic['name'] }}
                                    </p>
                                </div>
                                <span
                                    class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 text-xs font-bold rounded-full">{{ $clinic['visits'] }}
                                    visits</span>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400 text-sm">No data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Low Stock Medicines -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-xl">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Low Stock Alert</h3>
                    </div>
                    <div class="space-y-3">
                        @forelse($lowStockMedicines ?? [] as $medicine)
                            <div
                                class="flex items-center justify-between p-3 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 rounded-xl border border-red-200 dark:border-red-800 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $medicine['name'] }}</p>
                                </div>
                                <span
                                    class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">{{ $medicine['stock'] }}
                                    left</span>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div
                                    class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">All medicine in stock</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- System Stats -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-teal-100 dark:bg-teal-900/30 rounded-xl">
                            <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">System Status</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="flex justify-between items-center p-3 bg-gradient-to-r from-teal-50 to-cyan-50 dark:from-teal-900/20 dark:to-cyan-900/20 rounded-xl border border-teal-200 dark:border-teal-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-teal-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Active Users</p>
                            </div>
                            <p class="text-xl font-bold text-teal-600 dark:text-teal-400">{{ $activeUsers ?? 0 }}</p>
                        </div>
                        <div
                            class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Total Rooms</p>
                            </div>
                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $totalRooms ?? 0 }}</p>
                        </div>
                        <div
                            class="flex justify-between items-center p-3 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Suppliers</p>
                            </div>
                            <p class="text-xl font-bold text-purple-600 dark:text-purple-400">
                                {{ $totalSuppliers ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                /* Custom scrollbar */
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }

                .custom-scrollbar::-webkit-scrollbar-track {
                    background: transparent;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 3px;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #94a3b8;
                }

                .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #475569;
                }

                .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #64748b;
                }
            </style>

            {{-- Doctor Dashboard --}}
        @elseif (auth()->user()->role === 'doctor')
            <!-- Top Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- My Appointments Today -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase">Appointments
                            </p>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">
                                {{ $myAppointmentsToday ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">today</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Records -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-yellow-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase">Medical Records
                            </p>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">
                                {{ $pendingRecords ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">pending</p>
                        </div>
                        <div class="bg-yellow-100 dark:bg-yellow-900 rounded-full p-3">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Prescriptions -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase">Prescriptions
                            </p>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">
                                {{ $pendingPrescriptions ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">pending</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900 rounded-full p-3">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Lab Tests Pending -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-red-500 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase">Lab Tests</p>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">
                                {{ $labTestsPending ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">pending</p>
                        </div>
                        <div class="bg-red-100 dark:bg-red-900 rounded-full p-3">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule & Patients -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        My Schedule Today
                    </h3>
                    <div class="space-y-3">
                        @forelse($myScheduleToday ?? [] as $schedule)
                            <div
                                class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-white">
                                            {{ $schedule['clinic'] }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Clinic</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-blue-600 dark:text-blue-300">
                                            {{ $schedule['start_time'] }} - {{ $schedule['end_time'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No schedule for today</p>
                        @endforelse
                    </div>
                </div>

                <!-- Today's Patients -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Today's Patients
                    </h3>
                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        @forelse($myPatientsToday ?? [] as $patient)
                            <div
                                class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $patient['name'] }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $patient['time'] ?? 'Pending' }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No patients scheduled</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Lab & Radiology Requests -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 01-2-2z">
                            </path>
                        </svg>
                        Lab & Radiology Requests
                    </h3>
                    <div class="space-y-2">
                        <div class="flex justify-between p-2 bg-indigo-50 dark:bg-indigo-900 rounded">
                            <p class="text-sm text-gray-700 dark:text-gray-300">Lab Tests Pending</p>
                            <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $labTestsPending ?? 0 }}</p>
                        </div>
                        <div class="flex justify-between p-2 bg-cyan-50 dark:bg-cyan-900 rounded">
                            <p class="text-sm text-gray-700 dark:text-gray-300">Radiology Requests</p>
                            <p class="text-sm font-bold text-cyan-600 dark:text-cyan-400">{{ $radiologyPending ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Patient Statistics -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        My Statistics
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-700 dark:text-gray-300">Total Patients</p>
                            <p class="text-sm font-bold text-green-600">{{ $totalMyPatients ?? 0 }}</p>
                        </div>
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-700 dark:text-gray-300">This Month</p>
                            <p class="text-sm font-bold text-green-600">{{ $myPatientsMonth ?? 0 }}</p>
                        </div>
                        <div class="flex justify-between">
                            <p class="text-sm text-gray-700 dark:text-gray-300">Avg. Consultation</p>
                            <p class="text-sm font-bold text-green-600">{{ $avgConsultation ?? '0' }} min</p>
                        </div>
                    </div>
                </div>


                {{-- Cashier Dashboard --}}
            @elseif (auth()->user()->role === 'cashier')
                <!-- Top Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Today's Revenue -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase">Today's
                                    Revenue</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">Rp
                                    {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 mt-1">Rp
                                    {{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }} monthly</p>
                            </div>
                            <div class="bg-green-100 dark:bg-green-900 rounded-full p-3">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Payments -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-orange-500 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase">Pending
                                    Payments</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">
                                    {{ $pendingPayments ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Rp
                                    {{ number_format($pendingAmount ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-orange-100 dark:bg-orange-900 rounded-full p-3">
                                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Transactions -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase">
                                    Transactions</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">
                                    {{ $totalTransactions ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">today</p>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Balance -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-red-500 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase">Outstanding
                                </p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">Rp
                                    {{ number_format($outstandingBalance ?? 0, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 mt-1">total balance</p>
                            </div>
                            <div class="bg-red-100 dark:bg-red-900 rounded-full p-3">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Recent Transactions Table -->
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Recent Transactions (Today)
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left px-4 py-3 font-semibold text-gray-800 dark:text-white">
                                            Patient</th>
                                        <th class="text-right px-4 py-3 font-semibold text-gray-800 dark:text-white">
                                            Amount</th>
                                        <th class="text-center px-4 py-3 font-semibold text-gray-800 dark:text-white">
                                            Status</th>
                                        <th class="text-right px-4 py-3 font-semibold text-gray-800 dark:text-white">
                                            Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions ?? [] as $transaction)
                                        <tr
                                            class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-4 py-3 text-gray-800 dark:text-white">
                                                {{ $transaction['patient_name'] }}</td>
                                            <td
                                                class="px-4 py-3 font-semibold text-gray-800 dark:text-white text-right">
                                                Rp {{ number_format($transaction['amount'] ?? 0, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $transaction['status'] === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' }}">
                                                    {{ ucfirst($transaction['status'] ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 text-right">
                                                {{ $transaction['time'] ?? 'Today' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="text-center px-4 py-8 text-gray-500 dark:text-gray-400">No
                                                transactions yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Methods & Stats -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                </path>
                            </svg>
                            Payment Methods (Today)
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between p-2 bg-purple-50 dark:bg-purple-900 rounded">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Cash</p>
                                <p class="text-sm font-bold text-purple-600 dark:text-purple-400">Rp
                                    {{ number_format($cashPayment ?? 0, 0, ',', '.') }}</p>
                            </div>
                            {{-- <div class="flex justify-between p-2 bg-blue-50 dark:bg-blue-900 rounded">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Card</p>
                                <p class="text-sm font-bold text-blue-600 dark:text-blue-400">Rp
                                    {{ number_format($cardPayment ?? 0, 0, ',', '.') }}</p>
                            </div> --}}
                            <div class="flex justify-between p-2 bg-green-50 dark:bg-green-900 rounded">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Transfer</p>
                                <p class="text-sm font-bold text-green-600 dark:text-green-400">Rp
                                    {{ number_format($transferPayment ?? 0, 0, ',', '.') }}</p>
                            </div>
                            {{-- <div class="flex justify-between p-2 bg-orange-50 dark:bg-orange-900 rounded">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Insurance</p>
                                <p class="text-sm font-bold text-orange-600 dark:text-orange-400">Rp
                                    {{ number_format($insurancePayment ?? 0, 0, ',', '.') }}</p>
                            </div> --}}
                        </div>
                    </div>
                </div>

                <!-- Pending Verification -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pending Verification
                    </h3>
                    <div class="space-y-2">
                        @forelse($pendingVerification ?? [] as $pending)
                            <div
                                class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                        {{ $pending['patient_name'] }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $pending['amount'] }}</p>
                                </div>
                                <span
                                    class="text-xs font-semibold text-yellow-600 dark:text-yellow-400">{{ $pending['time'] }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">All payments verified</p>
                        @endforelse
                    </div>
                </div>

                {{-- Receptionist Dashboard --}}
            @elseif (auth()->user()->role === 'receptionist')
                <!-- Top Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Today's Appointments -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase">
                                    Appointments</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">
                                    {{ $todayAppointments ?? 0 }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">today</p>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- New Patients Today -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase">New
                                    Patients</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">
                                    {{ $newPatientsToday ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">registrations</p>
                            </div>
                            <div class="bg-green-100 dark:bg-green-900 rounded-full p-3">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Doctors on Duty -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold uppercase">Doctors on
                                    Duty</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white mt-2">
                                    {{ $doctorsOnDutyCount ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">present today</p>
                            </div>
                            <div class="bg-purple-100 dark:bg-purple-900 rounded-full p-3">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    
                </div>

                <!-- Main Sections -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Doctors on Duty Today -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Doctors on Duty Today
                        </h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @forelse($receptDoctorsOnDuty ?? [] as $doctor)
                                <div
                                    class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900 dark:to-emerald-900 rounded-lg">
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-white">{{ $doctor['name'] }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $doctor['specialization'] ?? 'General' }} -
                                            {{ $doctor['clinic'] ?? 'Clinic' }}</p>
                                    </div>
                                    <span
                                        class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 text-xs font-semibold rounded-full">Present</span>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No doctors scheduled for
                                    today</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Upcoming Appointments -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Upcoming Appointments
                        </h3>
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @forelse($upcomingAppointments ?? [] as $appointment)
                                <div
                                    class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-white">
                                                {{ $appointment['patient_name'] }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">with
                                                {{ $appointment['poli_name'] ?? 'Doctor' }}</p>
                                        </div>
                                        <span
                                            class="text-xs font-semibold text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300 px-2 py-1 rounded">{{ $appointment['time'] ?? 'Today' }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No upcoming appointments
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Bottom Sections -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Clinic Queue Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Clinic Queue
                        </h3>
                        <div class="space-y-3">
                            @forelse($clinicQueue ?? [] as $clinic)
                                <div class="p-3 bg-indigo-50 dark:bg-indigo-900 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                            {{ $clinic['name'] }}</p>
                                        <span
                                            class="px-2 py-1 bg-indigo-600 text-white text-xs font-bold rounded-full">{{ $clinic['queue'] }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-2">No queue data</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- New Patient Registrations -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            New Registrations
                        </h3>
                        <div class="space-y-2">
                            @forelse($newPatients ?? [] as $patient)
                                <div class="p-2 bg-green-50 dark:bg-green-900 rounded">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                        {{ $patient['name'] }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $patient['time'] }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-2">No new registrations</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Appointment Status -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            Today's Status
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Completed</p>
                                <p class="text-sm font-bold text-green-600">{{ $completedAppointments ?? 0 }}</p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-700 dark:text-gray-300">In Progress</p>
                                <p class="text-sm font-bold text-blue-600">{{ $inProgressAppointments ?? 0 }}</p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Scheduled</p>
                                <p class="text-sm font-bold text-yellow-600">{{ $scheduledAppointments ?? 0 }}</p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-700 dark:text-gray-300">Cancelled</p>
                                <p class="text-sm font-bold text-red-600">{{ $cancelledAppointments ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
        @endif
    </div>
    </div>
</x-app-layout>
