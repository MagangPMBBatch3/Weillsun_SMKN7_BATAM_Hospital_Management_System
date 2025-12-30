<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b-2 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex">
                    {{-- dashboard --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if (auth()->user()->role !== 'cashier')
                        {{-- master data --}}
                        <div class="flex items-center">
                            <x-dropdown align="right" width="60">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                        <span class="mr-2">Master Data</span>
                                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                {{-- Dropdown Content --}}
                                <x-slot name="content">
                                    <div
                                        class="py-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 w-60 transition-all duration-200">

                                        @if (auth()->user()->role === 'admin')
                                            {{--  User Section --}}
                                            <div
                                                class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                                                <span>User Management</span>
                                            </div>

                                            <div class="flex flex-col py-1">
                                                <x-dropdown-link :href="route('user.index')" :active="request()->routeIs('user.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    🧑 {{ __('User') }}
                                                </x-dropdown-link>

                                                <x-dropdown-link :href="route('usersProfile.index')" :active="request()->routeIs('usersProfile.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    🧩 {{ __('User Profiles') }}
                                                </x-dropdown-link>
                                            </div>
                                        @endif

                                        {{-- Medical Section --}}
                                        <div
                                            class="px-4 py-2 mt-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                                            <span>Healthcare Data</span>
                                        </div>

                                        <div class="flex flex-col py-1">
                                            @if (auth()->user()->role === 'admin')
                                                <x-dropdown-link :href="route('tenagaMedis.index')" :active="request()->routeIs('tenagaMedis.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    🩺 {{ __('Medical Personnels') }}
                                                </x-dropdown-link>
                                            @endif

                                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'doctor' || auth()->user()->role === 'receptionist')
                                                <x-dropdown-link :href="route('pasien.index')" :active="request()->routeIs('pasien.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    🧍 {{ __('Patient') }}
                                                </x-dropdown-link>
                                            @endif

                                            @if (auth()->user()->role === 'admin')
                                                <x-dropdown-link :href="route('obat.index')" :active="request()->routeIs('obat.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    💊 {{ __('Medicine') }}
                                                </x-dropdown-link>


                                                <x-dropdown-link :href="route('poli.index')" :active="request()->routeIs('poli.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    🏩 {{ __('Clinic') }}
                                                </x-dropdown-link>


                                                <x-dropdown-link :href="route('ruangan.index')" :active="request()->routeIs('ruangan.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    🛏️ {{ __('Room') }}
                                                </x-dropdown-link>

                                                <x-dropdown-link :href="route('supplier.index')" :active="request()->routeIs('supplier.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    🚚 {{ __('Supplier') }}
                                                </x-dropdown-link>
                                            @endif
                                        </div>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    @if (auth()->user()->role !== 'cashier')
                        {{-- medical services --}}
                        <div class="flex items-center">
                            <x-dropdown align="right" width="60">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                        <span class="mr-2">Medical Services</span>
                                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                {{-- Dropdown Content --}}
                                <x-slot name="content">
                                    <div
                                        class="py-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 w-60 transition-all duration-200">

                                        {{--  User Section --}}
                                        <div
                                            class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                                            <span>Services</span>
                                        </div>

                                        <div class="flex flex-col py-1">
                                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist')
                                                <x-dropdown-link :href="route('kunjungan.index')" :active="request()->routeIs('kunjungan.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    🚶‍♂️ {{ __('Visits') }}
                                                </x-dropdown-link>

                                                <x-dropdown-link :href="route('rawatInap.index')" :active="request()->routeIs('rawatInap.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    🏥 {{ __('Inpatient Care') }}
                                                </x-dropdown-link>
                                            @endif

                                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'doctor')
                                                <x-dropdown-link :href="route('rekamMedis.index')" :active="request()->routeIs('rekamMedis.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    📋 {{ __('Records') }}
                                                </x-dropdown-link>

                                                <x-dropdown-link :href="route('resepObat.index')" :active="request()->routeIs('resepObat.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    💊 {{ __('Prescriptions') }}
                                                </x-dropdown-link>

                                                <x-dropdown-link :href="route('radiologi.index')" :active="request()->routeIs('radiologi.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    ☢️ {{ __('Radiology') }}
                                                </x-dropdown-link>

                                                <x-dropdown-link :href="route('labPemeriksaan.index')" :active="request()->routeIs('labPemeriksaan.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    🔬 {{ __('Laboratory') }}
                                                </x-dropdown-link>
                                            @endif




                                        </div>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    @if (auth()->user()->role === 'admin')
                        {{-- Transaction --}}
                        <div class="flex items-center">
                            <x-dropdown align="right" width="60">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                        <span class="mr-2">Transaction</span>
                                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                {{-- Dropdown Content --}}
                                <x-slot name="content">
                                    <div
                                        class="py-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 w-60 transition-all duration-200">

                                        {{--  User Section --}}
                                        <div
                                            class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                                            <span>Payment</span>
                                        </div>

                                        <div class="flex flex-col py-1">
                                            <x-dropdown-link :href="route('pembayaranPasien.index')" :active="request()->routeIs('pembayaranPasien.index')"
                                                class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                💳 {{ __('Patient Payments') }}
                                            </x-dropdown-link>

                                            <x-dropdown-link :href="route('detailPembayaranPasien.index')" :active="request()->routeIs('detailPembayaranPasien.index')"
                                                class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                📑 {{ __('Patient Payment Details') }}
                                            </x-dropdown-link>

                                            <x-dropdown-link :href="route('pembelianObat.index')" :active="request()->routeIs('pembelianObat.index')"
                                                class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                🧾 {{ __('Drug Purchases') }}
                                            </x-dropdown-link>

                                            <x-dropdown-link :href="route('detailPembelianObat.index')" :active="request()->routeIs('detailPembelianObat.index')"
                                                class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                📦 {{ __('Drug Purchases Details') }}
                                            </x-dropdown-link>

                                            <x-dropdown-link :href="route('pembayaranSupplier.index')" :active="request()->routeIs('pembayaranSupplier.index')"
                                                class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                🏦 {{ __('Supplier Payments') }}
                                            </x-dropdown-link>

                                        </div>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    @if (auth()->user()->role === 'cashier')
                        <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex">

                            <x-nav-link :href="route('pembayaranPasien.index')" :active="request()->routeIs('pembayaranPasien.index')">
                                💳 Patient Payments
                            </x-nav-link>

                            <x-nav-link :href="route('detailPembayaranPasien.index')" :active="request()->routeIs('detailPembayaranPasien.index')">
                                📑 Payment Details
                            </x-nav-link>

                            <x-nav-link :href="route('pembelianObat.index')" :active="request()->routeIs('pembelianObat.index')">
                                🧾 Drug Purchases
                            </x-nav-link>

                            <x-nav-link :href="route('detailPembelianObat.index')" :active="request()->routeIs('detailPembelianObat.index')">
                                📦 Purchase Details
                            </x-nav-link>

                            <x-nav-link :href="route('pembayaranSupplier.index')" :active="request()->routeIs('pembayaranSupplier.index')">
                                🏦 Supplier Payments
                            </x-nav-link>

                        </div>
                    @endif

                    @if (auth()->user()->role !== 'cashier')
                        {{-- Schedule --}}
                        <div class="flex items-center">
                            <x-dropdown align="right" width="60">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                        <span class="mr-2">Schedule</span>
                                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                {{-- Dropdown Content --}}
                                <x-slot name="content">
                                    <div
                                        class="py-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 w-60 transition-all duration-200">

                                        <div class="flex flex-col py-1">
                                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist')
                                                <x-dropdown-link :href="route('kunjunganUlang.index')" :active="request()->routeIs('kunjunganUlang.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    🔁 {{ __('Return Visits') }}
                                                </x-dropdown-link>
                                            @endif

                                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'doctor')
                                                <x-dropdown-link :href="route('jadwalTenagaMedis.index')" :active="request()->routeIs('jadwalTenagaMedis.index')"
                                                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700  transition-colors duration-150">
                                                    📅 {{ __("Doctor's Schedule") }}
                                                </x-dropdown-link>
                                            @endif

                                        </div>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex capitalize items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" class="text-red-600 font-semibold"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
