<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
</style>

<nav x-data="{ sidebarOpen: true, expandedMenu: null }"
    class="fixed flex flex-col no-scrollbar left-0 top-0 overflow-y-auto h-screen w-64 bg-gradient-to-b from-blue-600 to-blue-700 dark:from-gray-900 dark:to-gray-800 shadow-2xl z-50">
    <!-- Logo Section -->
    <div class="sticky top-0 bg-white p-4 flex items-center gap-3 border-b-[10px] border-blue-600 border-dashed">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div
                class="w-10 h-10 bg-white rounded-lg flex items-center justify-center group-hover:bg-blue-50 transition-colors duration-200">
                <img src="{{ asset('MedicaHub-Logo.png') }}" alt="">
            </div>
            <span
                class="bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent font-bold text-lg sm:inline">MedicaHub</span>
        </a>
    </div>

    <!-- Navigation Menu -->
    <div class="p-4 space-y-2">
        <!-- Dashboard Link -->
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white text-blue-600 shadow-md' : 'text-white hover:bg-blue-500 dark:hover:bg-gray-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Master Data Menu -->
        @if (auth()->user()->role !== 'cashier')
            <div x-data="{ open: {{ request()->routeIs('user.index', 'usersProfile.index', 'tenagaMedis.index', 'pasien.index', 'obat.index', 'poli.index', 'ruangan.index', 'supplier.index') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-white hover:bg-blue-500 dark:hover:bg-gray-700 transition-all duration-200 group">
                    <div class="flex items-center gap-3">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122" />
                        </svg>

                        <span class="font-medium">Master Data</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </button>

                <div x-show="open" x-transition
                    class="ml-4 mt-1 space-y-1 border-l-2 border-blue-500 dark:border-gray-600">
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('user.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('user.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>🧑</span>
                            <span>User</span>
                        </a>

                        <a href="{{ route('usersProfile.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('usersProfile.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>🧩</span>
                            <span>User Profiles</span>
                        </a>
                    @endif

                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('tenagaMedis.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('tenagaMedis.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>🩺</span>
                            <span>Medical Personnels</span>
                        </a>
                    @endif

                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'receptionist')
                        <a href="{{ route('pasien.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('pasien.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>🧍</span>
                            <span>Patient</span>
                        </a>
                    @endif

                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('obat.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('obat.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>💊</span>
                            <span>Medicine</span>
                        </a>

                        <a href="{{ route('poli.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('poli.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>🏩</span>
                            <span>Clinic</span>
                        </a>

                        <a href="{{ route('ruangan.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('ruangan.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>🛏️</span>
                            <span>Room</span>
                        </a>

                        <a href="{{ route('supplier.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('supplier.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>🚚</span>
                            <span>Supplier</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Medical Services Menu -->
        @if (auth()->user()->role !== 'cashier')
            <div x-data="{ open: {{ request()->routeIs('kunjungan.index', 'rawatInap.index', 'rekamMedis.index', 'resepObat.index', 'radiologi.index', 'labPemeriksaan.index') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-white hover:bg-blue-500 dark:hover:bg-gray-700 transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span class="font-medium">Medical Services</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </button>

                <div x-show="open" x-transition
                    class="ml-4 mt-1 space-y-1 border-l-2 border-blue-500 dark:border-gray-600">
                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist')
                        <a href="{{ route('kunjungan.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('kunjungan.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>🚶‍♂️</span>
                            <span>Visits</span>
                        </a>

                        <a href="{{ route('rawatInap.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('rawatInap.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>🏥</span>
                            <span>Inpatient Care</span>
                        </a>
                    @endif

                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'doctor')
                        <a href="{{ route('rekamMedis.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('rekamMedis.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>📋</span>
                            <span>Records</span>
                        </a>

                        <a href="{{ route('resepObat.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('resepObat.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>💊</span>
                            <span>Prescriptions</span>
                        </a>

                        <a href="{{ route('radiologi.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('radiologi.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>☢️</span>
                            <span>Radiology</span>
                        </a>

                        <a href="{{ route('labPemeriksaan.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('labPemeriksaan.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <span>🔬</span>
                            <span>Laboratory</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Transaction Menu -->
        @if (auth()->user()->role === 'admin')
            <div x-data="{ open: {{ request()->routeIs('pembayaranPasien.index', 'detailPembayaranPasien.index', 'pembelianObat.index', 'detailPembelianObat.index', 'pembayaranSupplier.index') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-white hover:bg-blue-500 dark:hover:bg-gray-700 transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span class="font-medium">Transaction</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </button>

                <div x-show="open" x-transition
                    class="ml-4 mt-1 space-y-1 border-l-2 border-blue-500 dark:border-gray-600">
                    <a href="{{ route('pembayaranPasien.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('pembayaranPasien.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <span>💳</span>
                        <span>Patient Payments</span>
                    </a>

                    <a href="{{ route('detailPembayaranPasien.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('detailPembayaranPasien.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <span>📑</span>
                        <span>Payment Details</span>
                    </a>

                    <a href="{{ route('pembelianObat.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('pembelianObat.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <span>🧾</span>
                        <span>Drug Purchases</span>
                    </a>

                    <a href="{{ route('detailPembelianObat.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('detailPembelianObat.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <span>📦</span>
                        <span>Purchase Details</span>
                    </a>

                    <a href="{{ route('pembayaranSupplier.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('pembayaranSupplier.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <span>🏦</span>
                        <span>Supplier Payments</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Cashier Menu -->
        @if (auth()->user()->role === 'cashier')
            <a href="{{ route('pembayaranPasien.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-blue-500 dark:hover:bg-gray-700 transition-all duration-200 {{ request()->routeIs('pembayaranPasien.index') ? 'bg-white text-blue-600' : '' }}">
                <span>💳</span>
                <span class="font-medium">Patient Payments</span>
            </a>

            <a href="{{ route('detailPembayaranPasien.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-blue-500 dark:hover:bg-gray-700 transition-all duration-200 {{ request()->routeIs('detailPembayaranPasien.index') ? 'bg-white text-blue-600' : '' }}">
                <span>📑</span>
                <span class="font-medium">Payment Details</span>
            </a>

            <a href="{{ route('pembelianObat.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-blue-500 dark:hover:bg-gray-700 transition-all duration-200 {{ request()->routeIs('pembelianObat.index') ? 'bg-white text-blue-600' : '' }}">
                <span>🧾</span>
                <span class="font-medium">Drug Purchases</span>
            </a>

            <a href="{{ route('detailPembelianObat.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-blue-500 dark:hover:bg-gray-700 transition-all duration-200 {{ request()->routeIs('detailPembelianObat.index') ? 'bg-white text-blue-600' : '' }}">
                <span>📦</span>
                <span class="font-medium">Purchase Details</span>
            </a>

            <a href="{{ route('pembayaranSupplier.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-white hover:bg-blue-500 dark:hover:bg-gray-700 transition-all duration-200 {{ request()->routeIs('pembayaranSupplier.index') ? 'bg-white text-blue-600' : '' }}">
                <span>🏦</span>
                <span class="font-medium">Supplier Payments</span>
            </a>
        @endif

        <!-- Schedule Menu -->
        <div x-data="{ open: {{ request()->routeIs('kunjunganUlang.index', 'jadwalTenagaMedis.index', 'liburTenagaMedis') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-white hover:bg-blue-500 dark:hover:bg-gray-700 transition-all duration-200">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span class="font-medium">Schedule</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </button>

            <div x-show="open" x-transition
                class="ml-4 mt-1 space-y-1 border-l-2 border-blue-500 dark:border-gray-600">
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist')
                    <a href="{{ route('kunjunganUlang.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('kunjunganUlang.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <span>🔁</span>
                        <span>Return Visits</span>
                    </a>
                @endif

                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'receptionist')
                    <a href="{{ route('jadwalTenagaMedis.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('jadwalTenagaMedis.index') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                        <span>📅</span>
                        <span>Staff Schedule</span>
                    </a>
                @endif

                <a href="{{ route('liburTenagaMedis') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-r-lg text-sm transition-all duration-200 {{ request()->routeIs('liburTenagaMedis') ? 'bg-white text-blue-600' : 'text-blue-100 hover:text-white hover:bg-blue-500 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                    <span>🚶‍♂️</span>
                    <span>Staff Leave</span>
                </a>
            </div>
        </div>
    </div>

    <!-- User Profile Section (Bottom) -->
    <div
        class="sticky bottom-0 bg-gradient-to-r from-blue-600 to-blue-700 dark:bg-gradient-to-r dark:from-gray-800 dark:to-gray-900 p-4 border-t border-blue-500/30 dark:border-gray-700 mt-auto backdrop-blur-sm">
        <div x-data="{ profileOpen: false }" class="relative">
            <!-- Profile Info -->
            <div class="flex items-center gap-3 mb-3">
                <!-- Avatar -->
                <div class="relative">
                    <div
                        class="w-12 h-12 rounded-xl overflow-hidden flex items-center justify-centershadow-lg ring-2 ring-white/50">
                        <img class=" w-full h-full object-cover"
                        src="{{ Auth::user()->profile?->foto ? asset('storage/' . Auth::user()->profile->foto) : asset('default_pp.jpg') }}">
                    </div>
                    <div
                        class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-blue-600 dark:border-gray-800 rounded-full">
                    </div>
                </div>

                <!-- User Info -->
                <div class="flex-1 min-w-0">
                    <p class="text-white font-semibold text-sm truncate">{{ Auth::user()->name }}</p>
                    <p class="text-blue-100 dark:text-gray-400 text-xs truncate">{{ Auth::user()->email }}</p>
                </div>

                <!-- Settings Button -->
                <button @click="profileOpen = !profileOpen"
                    class="flex-shrink-0 w-9 h-9 flex items-center justify-center text-white hover:bg-white/20 rounded-lg transition-all duration-200 hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
            </div>

            <!-- Dropdown Menu -->
            <div x-show="profileOpen" @click.outside="profileOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="absolute bottom-full left-0 right-0 mb-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                style="display: none;">

                <!-- Profile Link -->
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <span class="font-medium">My Profile</span>
                </a>

                <!-- Divider -->
                <div class="border-t border-gray-200 dark:border-gray-700"></div>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" onclick="event.preventDefault(); this.closest('form').submit();"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </div>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

</nav>

<!-- Spacer untuk konten agar tidak tertindih sidebar -->
<div class="ml-64"></div>
