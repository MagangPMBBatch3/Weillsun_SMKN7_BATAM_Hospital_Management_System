<x-app-layout>
    <div class="pb-8 pt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
           <div class="mb-8 relative overflow-hidden bg-gradient-to-r from-blue-600 via-blue-500 to-purple-600 rounded-2xl shadow-xl">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-2xl"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/20 rounded-full -ml-24 -mb-24 blur-xl"></div>
                
                <!-- Content -->
                <div class="relative flex items-center justify-between px-8 pr-0 py-6">
                    <!-- Left: Title & Icon -->
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg border border-white/30">
                            <i class='bx bx-user-circle text-white text-4xl'></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white drop-shadow-lg">Account Settings</h1>
                            <p class="text-blue-100 mt-1 text-sm font-medium">Manage your profile information, security, and account preferences</p>
                        </div>
                    </div>

                    <!-- Right: Status Badge -->
                    <div class="flex items-center gap-3 px-6 py-4 bg-white/20 backdrop-blur-sm rounded-l-full border border-white/30 shadow-lg">
                        <div class="w-12 h-12 bg-white/30 rounded-full flex items-center justify-center">
                            <i class='bx bx-check-circle text-white text-2xl'></i>
                        </div>
                        <div>
                            <p class="text-sm text-blue-100">Status</p>
                            <p class="text-xl font-bold text-white">Active</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column - Main Content (2/3 width) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Profile Information -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-700">
                        <div class="p-6 sm:p-8">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Update Password -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-200 dark:border-gray-700">
                        <div class="p-6 sm:p-8">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <!-- Right Column - Sidebar (1/3 width) -->
                <div class="lg:col-span-1 space-y-8">
                    
                    <!-- Quick Stats Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl border border-blue-200 dark:border-blue-800 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                                <i class='bx bx-user-check text-white text-xl'></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Account Status</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-white/50 dark:bg-gray-800/50 rounded-xl">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Member Since</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->created_at->format('M Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white/50 dark:bg-gray-800/50 rounded-xl">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Account Type</span>
                                <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ ucfirst(Auth::user()->role ?? 'User') }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-white/50 dark:bg-gray-800/50 rounded-xl">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Email Status</span>
                                @if(Auth::user()->email_verified_at)
                                    <span class="flex items-center gap-1 text-sm font-semibold text-green-600 dark:text-green-400">
                                        <i class='bx bx-check-circle'></i>
                                        Verified
                                    </span>
                                @else
                                    <span class="flex items-center gap-1 text-sm font-semibold text-yellow-600 dark:text-yellow-400">
                                        <i class='bx bx-error-circle'></i>
                                        Unverified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Security Tips Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
                                <i class='bx bx-shield-quarter text-white text-xl'></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Security Tips</h3>
                        </div>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <i class='bx bx-check text-green-500 text-lg mt-0.5'></i>
                                <span>Use a strong, unique password</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <i class='bx bx-check text-green-500 text-lg mt-0.5'></i>
                                <span>Enable two-factor authentication</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <i class='bx bx-check text-green-500 text-lg mt-0.5'></i>
                                <span>Keep your contact info updated</span>
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <i class='bx bx-check text-green-500 text-lg mt-0.5'></i>
                                <span>Review account activity regularly</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Recent Activity Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                                <i class='bx bx-clock-3 text-white text-xl'></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class='bx bx-arrow-in-right-stroke-circle-half text-blue-600 dark:text-blue-400'></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Account Age</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Member for {{ Auth::user()->created_at->diffForHumans(null, true) }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class='bx bx-edit text-green-600 dark:text-green-400'></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Profile Updated</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Auth::user()->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class='bx bx-user-plus text-purple-600 dark:text-purple-400'></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Account Created</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Auth::user()->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Account Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-red-200 dark:border-red-900 p-6 pb-7">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center">
                                <i class='bx bx-trash text-white text-xl'></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Danger Zone</h3>
                        </div>
                        @include('profile.partials.delete-user-form')
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>