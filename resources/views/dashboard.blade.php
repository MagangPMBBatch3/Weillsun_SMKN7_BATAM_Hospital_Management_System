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
                <!-- rawat inap -->
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

            {{-- Doctor Dashboard --}}
        @elseif (auth()->user()->role === 'doctor')
            <!-- Doctor Header -->
            <div
                class="mb-8 relative overflow-hidden bg-gradient-to-r from-blue-600 via-blue-500 to-purple-600 rounded-2xl shadow-xl">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-2xl"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/20 rounded-full -ml-24 -mb-24 blur-xl">
                </div>

                <!-- Content -->
                <div class="relative flex items-center gap-6 px-8 py-6">
                    <!-- Doctor Photo -->
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full blur-lg opacity-50">
                        </div>
                        <img src="{{ $doctorInfo['photo'] }}" alt="{{ $doctorInfo['name'] }}"
                            class="relative w-24 h-24 rounded-full object-cover ring-4 ring-white shadow-2xl">
                    </div>

                    <!-- Doctor Info -->
                    <div class="flex-grow">
                        <h2 class="text-3xl font-bold text-white drop-shadow-lg mb-2">
                            {{ $doctorInfo['name'] }}
                        </h2>
                        <div class="flex items-center gap-3 mb-3">
                            <span
                                class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-semibold border border-white/30">
                                {{ $doctorInfo['specialization'] }}
                            </span>
                            <span class="text-blue-100 text-sm">STR: {{ $doctorInfo['no_str'] }}</span>
                        </div>

                        <!-- Schedule Info -->
                        @if ($scheduleInfo['status'] === 'active')
                            <div
                                class="flex items-center gap-3 px-4 py-2 bg-green-500/20 backdrop-blur-sm rounded-xl border border-green-400/30">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                    <span class="text-white font-semibold text-sm">Active Schedule</span>
                                </div>
                                <span class="text-blue-100 text-sm">
                                    {{ $scheduleInfo['day'] }}, {{ $scheduleInfo['start_time'] }} -
                                    {{ $scheduleInfo['end_time'] }} | {{ $scheduleInfo['poli_name'] }}
                                </span>
                            </div>
                        @elseif($scheduleInfo['status'] === 'off')
                            <div
                                class="flex items-center gap-3 px-4 py-2 bg-red-500/20 backdrop-blur-sm rounded-xl border border-red-400/30">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span
                                        class="text-white font-semibold text-sm">{{ ucfirst($scheduleInfo['jenis']) }}</span>
                                </div>
                                @if ($scheduleInfo['note'])
                                    <span class="text-red-100 text-sm">{{ $scheduleInfo['note'] }}</span>
                                @endif
                            </div>
                        @else
                            <div
                                class="flex items-center gap-2 px-4 py-2 bg-gray-500/20 backdrop-blur-sm rounded-xl border border-gray-400/30">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="text-white font-semibold text-sm">{{ $scheduleInfo['message'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Patients -->
                <div
                    class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-blue-200 dark:border-blue-700 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-blue-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-2">
                            Total Patients</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ $stats['total_patients'] }}</p>
                    </div>
                </div>

                <!-- Today's Records -->
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
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400 uppercase tracking-wide mb-2">
                            Today's Records</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ $stats['today_records'] }}</p>
                    </div>
                </div>

                <!-- Follow-ups Today -->
                <div
                    class="group bg-gradient-to-br from-cyan-50 to-blue-100 dark:from-cyan-900/30 dark:to-blue-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-cyan-200 dark:border-cyan-700 relative overflow-hidden">
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
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-cyan-600 dark:text-cyan-400 uppercase tracking-wide mb-2">
                            Follow-ups Today</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ $stats['today_followups'] }}
                        </p>
                    </div>
                </div>

                <!-- Pending Actions -->
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
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p
                            class="text-sm font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wide mb-2">
                            Pending Actions</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white">
                            {{ $stats['pending_labs'] + $stats['pending_radiology'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column (2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Today's Follow-up Appointments -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Today's Follow-up
                                    Appointments</h3>
                            </div>
                            <span
                                class="px-3 py-1 bg-blue-500 text-white text-sm font-bold shadow rounded-full">{{ $todayFollowUps->count() }}</span>
                        </div>

                        <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
                            @if ($todayFollowUps->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($todayFollowUps as $followup)
                                        <div
                                            class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-300">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-4 flex-1">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 text-lg rounded-lg flex items-center justify-center text-white font-bold">
                                                            {{ substr($followup->pasien_nama, 0, 1) }}

                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-semibold text-gray-900 dark:text-white">
                                                            {{ $followup->pasien_nama }}</p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span
                                                                class="px-2 py-1 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400 text-xs font-medium rounded-full">{{ $followup->nama_poli }}</span>
                                                            @if ($followup->status === 'scheduled')
                                                                <span
                                                                    class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-xs font-medium rounded-full">Scheduled</span>
                                                            @elseif($followup->status === 'completed')
                                                                <span
                                                                    class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium rounded-full">Completed</span>
                                                            @else
                                                                <span
                                                                    class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-medium rounded-full">Cancelled</span>
                                                            @endif
                                                        </div>
                                                        @if ($followup->catatan)
                                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                                {{ Str::limit($followup->catatan, 50) }}</p>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="text-right">
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $followup->jam_ulang }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400">No follow-up appointments today</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Recent Activities</h3>
                        </div>

                        <div class="max-h-[400px] overflow-y-auto custom-scrollbar space-y-3">
                            @if ($recentActivities->count() > 0)
                                @foreach ($recentActivities as $activity)
                                    <div
                                        class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-300">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-start gap-3 flex-1">
                                                @if ($activity->type === 'medical_record')
                                                    <div
                                                        class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="font-semibold text-gray-900 dark:text-white">Medical
                                                            Record - {{ $activity->pasien_nama }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            {{ $activity->detail }}</p>
                                                    </div>
                                                @elseif($activity->type === 'prescription')
                                                    <div
                                                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="font-semibold text-gray-900 dark:text-white">
                                                            Prescription - {{ $activity->pasien_nama }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            {{ $activity->detail }}</p>
                                                    </div>
                                                @elseif($activity->type === 'lab')
                                                    <div
                                                        class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="font-semibold text-gray-900 dark:text-white">Lab
                                                            Request - {{ $activity->pasien_nama }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            {{ $activity->detail }}</p>
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="font-semibold text-gray-900 dark:text-white">
                                                            Radiology - {{ $activity->pasien_nama }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            {{ $activity->detail }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap ml-4">
                                                {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400">No recent activities</p>
                                </div>
                            @endif
                        </div>
                    </div>


                </div>

                <!-- Right Column (1/3) -->
                <div class="space-y-6">
                    <!-- Pending Actions -->
                    {{-- <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pending Actions</h3>
                        </div>
                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center p-3 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                        </path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Lab Results</span>
                                </div>
                                <span
                                    class="px-2 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full">{{ $stats['pending_labs'] }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center p-3 bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl border border-orange-200 dark:border-orange-800">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                                        </path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Radiology</span>
                                </div>
                                <span
                                    class="px-2 py-1 bg-orange-500 text-white text-xs font-bold rounded-full">{{ $stats['pending_radiology'] }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center p-3 bg-gradient-to-r from-gray-50 to-slate-50 dark:from-gray-700/50 dark:to-slate-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Unpaid
                                        Prescriptions</span>
                                </div>
                                <span
                                    class="px-2 py-1 bg-gray-500 text-white text-xs font-bold rounded-full">{{ $stats['unpaid_prescriptions'] }}</span>
                            </div>
                        </div>
                    </div> --}}

                    <!-- My Inpatients -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m0 0h-.581m0 0H5m0 0h2">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">My Inpatients</h3>
                        </div>
                        <div class="space-y-3 max-h-[300px] overflow-y-auto custom-scrollbar pr-2">
                            @if ($myInpatients->count() > 0)
                                @foreach ($myInpatients as $inpatient)
                                    <div
                                        class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800">
                                        <div class="flex justify-between items-center gap-3">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                                    {{ substr($inpatient->pasien_nama, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900 dark:text-white">
                                                        {{ $inpatient->pasien_nama }}</p>
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="text-sm text-gray-600 dark:text-gray-400">{{ $inpatient->nama_ruangan }}</span>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $inpatient->days_admitted }} day(s) admitted
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m0 0h-.581m0 0H5m0 0h2">
                                        </path>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">No active inpatients</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Visit Chart -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Visits - Last 7 Days</h3>
                        </div>
                        <canvas id="visitChart" class="w-full"></canvas>
                    </div>

                    <!-- Top Diagnoses This Month -->
                    {{-- <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top Diagnoses</h3>
                        </div>
                        <div class="space-y-4">
                            @if ($topDiagnoses->count() > 0)
                                @foreach ($topDiagnoses as $diagnosis)
                                    <div>
                                        <div class="flex justify-between mb-2">
                                            <span
                                                class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Str::limit($diagnosis->diagnosis, 25) }}</span>
                                            <span
                                                class="text-sm font-bold text-cyan-600 dark:text-cyan-400">{{ $diagnosis->total }}</span>
                                        </div>
                                        <div
                                            class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-2 rounded-full transition-all duration-500"
                                                style="width: {{ ($diagnosis->total / $topDiagnoses->max('total')) * 100 }}%">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">No diagnoses this month</p>
                                </div>
                            @endif
                        </div>
                    </div> --}}
                </div>
            </div>

            <!-- Chart.js Script -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('visitChart');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($visitChart->pluck('date')),
                        datasets: [{
                            label: 'Patients Visited',
                            data: @json($visitChart->pluck('count')),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
                                    font: {
                                        size: 12,
                                        weight: 600
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                                },
                                grid: {
                                    color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                                }
                            },
                            x: {
                                ticks: {
                                    color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                                },
                                grid: {
                                    color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                                }
                            }
                        }
                    }
                });
            </script>

            {{-- Cashier Dashboard --}}
        @elseif (auth()->user()->role === 'cashier')
            <!-- Top Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Today's Revenue -->
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
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400 uppercase tracking-wide mb-2">
                            Today's Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Rp
                            {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Rp
                            {{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }} monthly</p>
                    </div>
                </div>

                <!-- Pending Payments -->
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
                                        d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p
                            class="text-sm font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wide mb-2">
                            Patient Payments</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $patientPayments ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Rp
                            {{ number_format($patientAmount ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Total Transactions -->
                <div
                    class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-blue-200 dark:border-blue-700 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-blue-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-2">
                            Transactions</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">{{ $totalTransactions ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">processed today</p>
                    </div>
                </div>

                <!-- Outstanding Balance -->
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
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-red-600 dark:text-red-400 uppercase tracking-wide mb-2">
                            Outstanding</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Rp
                            {{ number_format($outstandingBalance ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">total balance</p>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Recent Transactions Table -->
                <div
                    class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Recent Transactions (Today)</h3>
                    </div>

                    <div class="overflow-x-auto max-h-[500px] overflow-y-auto custom-scrollbar">
                        <table class="w-full text-sm">
                            <thead class="sticky top-0 bg-gray-50 dark:bg-gray-700 z-10">
                                <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                                    <th class="text-left px-4 py-3 font-semibold text-gray-800 dark:text-white">Patient
                                    </th>
                                    <th class="text-right px-4 py-3 font-semibold text-gray-800 dark:text-white">Amount
                                    </th>
                                    <th class="text-center px-4 py-3 font-semibold text-gray-800 dark:text-white">
                                        Status</th>
                                    <th class="text-right px-4 py-3 font-semibold text-gray-800 dark:text-white">Time
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions ?? [] as $transaction)
                                    <tr
                                        class="border-b border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                                                    {{ substr($transaction['patient_name'], 0, 1) }}
                                                </div>
                                                <span
                                                    class="font-medium text-gray-800 dark:text-white">{{ $transaction['patient_name'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 font-bold text-gray-900 dark:text-white text-right">
                                            Rp {{ number_format($transaction['amount'] ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @if ($transaction['status'] === 'paid')
                                                <span
                                                    class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-semibold rounded-full">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Paid
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-xs font-semibold rounded-full">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400 text-right">
                                            {{ $transaction['time'] ?? 'Today' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center px-4 py-12">
                                            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                </path>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400">No transactions yet</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment Methods & Stats -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Payment Methods</h3>
                    </div>

                    <div class="space-y-4">
                        <!-- Cash Payment -->
                        <div
                            class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl border border-purple-200 dark:border-purple-800 hover:shadow-md transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Cash</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Today</p>
                                    </div>
                                </div>
                                <p class="text-lg font-bold text-purple-600 dark:text-purple-400">
                                    Rp {{ number_format($cashPayment ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Transfer Payment -->
                        <div
                            class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800 hover:shadow-md transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Transfer</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Today</p>
                                    </div>
                                </div>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($transferPayment ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Total Today -->
                        <div
                            class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border-2 border-blue-300 dark:border-blue-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Total Today</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">All Methods</p>
                                    </div>
                                </div>
                                <p class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                    Rp {{ number_format(($cashPayment ?? 0) + ($transferPayment ?? 0), 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Verification -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pending Verification</h3>
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                    @forelse($pendingVerification ?? [] as $pending)
                        <div
                            class="p-4 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800 hover:shadow-md transition-all duration-300">
                            <div class="flex items-start justify-between gap-3">

                                <div class="flex gap-3 items-center">

                                <div
                                    class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                    {{ substr($pending['patient_name'], 0, 1) }}
                                </div>

                                    <div class="">
                                        <p class="font-semibold text-gray-900 dark:text-white truncate">
                                            {{ $pending['patient_name'] }}</p>
                                        <p class="text-sm font-medium text-yellow-700 dark:text-yellow-400 mt-1">
                                            {{ $pending['amount'] }}</p>
                                    </div>
                                </div>


                                    <div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $pending['time'] }}</p>
                                    </div>

                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">All payments verified</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Receptionist Dashboard --}}
        @elseif (auth()->user()->role === 'receptionist')
            <!-- Top Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Today's Appointments -->
                <div
                    class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-6 border border-blue-200 dark:border-blue-700 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="p-3 bg-blue-500 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-2">
                            Appointments</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ $todayAppointments ?? 0 }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">scheduled today</p>
                    </div>
                </div>

                <!-- New Patients Today -->
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
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400 uppercase tracking-wide mb-2">
                            New Patients</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ $newPatientsToday ?? 0 }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">new registrations</p>
                    </div>
                </div>

                <!-- Doctors on Duty -->
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
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="flex items-center gap-1">
                                <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                                <span
                                    class="text-xs font-semibold text-purple-700 dark:text-purple-300">{{ $doctorsOnDutyCount ?? 0 }}
                                    active</span>
                            </div>
                        </div>
                        <p
                            class="text-sm font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-2">
                            Doctors on Duty</p>
                        <p class="text-4xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ $doctorsOnDutyCount ?? 0 }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">present today</p>
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
                    <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                        @forelse($receptDoctorsOnDuty ?? [] as $doctor)
                            <div
                                class="group flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-lg group-hover:scale-110 transition-transform duration-300">
                                        {{ substr($doctor['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ $doctor['name'] }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $doctor['specialization'] ?? 'General' }} -
                                            {{ $doctor['clinic'] ?? 'Clinic' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span
                                        class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">Present</span>
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

                <!-- Upcoming Appointments -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Upcoming Appointments</h3>
                    </div>
                    <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                        @forelse($upcomingAppointments ?? [] as $appointment)
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 hover:shadow-md transition-all duration-300">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-bold">
                                            {{ substr($appointment['patient_name'], 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900 dark:text-white mb-1">
                                                <span
                                                    class="text-blue-600 dark:text-blue-400">{{ $appointment['patient_name'] }}</span>
                                                in {{ $appointment['poli_name'] }}
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">with
                                                {{ $appointment['doctor_name'] ?? 'Doctor' }}</p>
                                        </div>
                                    </div>
                                    <span
                                        class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-full whitespace-nowrap">{{ $appointment['time'] ?? 'Today' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">No upcoming appointments</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Bottom Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Clinic Queue Status -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Clinic Queue</h3>
                    </div>
                    <div class="space-y-3 max-h-[300px] overflow-y-auto custom-scrollbar pr-2">
                        @forelse($clinicQueue ?? [] as $index => $clinic)
                            <div
                                class="flex items-center justify-between p-3 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $clinic['name'] }}</p>
                                </div>
                                <span
                                    class="px-3 py-1 bg-indigo-500 text-white text-xs font-bold rounded-full">{{ $clinic['queue'] }}</span>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400 text-sm">No queue data</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- New Patient Registrations -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-xl">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">New Registrations</h3>
                    </div>
                    <div class="space-y-3 max-h-[300px] overflow-y-auto custom-scrollbar pr-2">
                        @forelse($newPatients ?? [] as $patient)
                            <div
                                class="p-3 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($patient['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $patient['name'] }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $patient['time'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400 text-sm">No new registrations</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Appointment Status -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl">
                            <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Visit Status</h3>
                    </div>
                    <div class="space-y-4">
                        <div
                            class="flex justify-between items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Completed</p>
                            </div>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">
                                {{ $completedAppointments ?? 0 }}</p>
                        </div>
                        <div
                            class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Scheduled</p>
                            </div>
                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $scheduledAppointments ?? 0 }}</p>
                        </div>
                        <div
                            class="flex justify-between items-center p-3 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 rounded-xl border border-red-200 dark:border-red-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Cancelled</p>
                            </div>
                            <p class="text-xl font-bold text-red-600 dark:text-red-400">
                                {{ $cancelledAppointments ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

        @endif
    </div>
    </div>
</x-app-layout>
