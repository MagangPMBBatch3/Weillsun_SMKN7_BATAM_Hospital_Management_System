<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <x-slot name="header">
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-gray-900/60 rounded-2xl p-5 shadow-md border border-gray-200/50 dark:border-gray-700/50">

            {{-- title --}}
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-800 dark:text-gray-100 flex items-center gap-3">
                <i class='bx bx-user text-3xl text-blue-500'></i>
                <span class=" tracking-wider">User Profiles</span>
            </h2>

            {{-- Search & Buttons --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full lg:w-auto">

                {{-- Search Bar --}}
                <div class="relative w-full sm:w-72">
                    <input type="text" id="search" placeholder="Search..."
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-sm px-4 py-2.5 pl-9 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-200"
                        oninput="searchUsersProfile()">
                    <i class='bx bx-search absolute left-3 top-3 h-5 w-5 text-gray-600 '></i>
                </div>

                {{-- Tombol Aktif / Arsip --}}
                @if (auth()->user()->role === 'admin')
                    <div class="flex items-center gap-2 justify-center">
                        <button id="btnActive"
                            class="px-5 py-2.5 rounded-xl bg-blue-500 text-white font-semibold shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200 active:scale-95"
                            onclick="showTable(true)">
                            Active
                        </button>
                        <button id="btnArchive"
                            class="px-5 py-2.5 rounded-xl bg-gray-300 text-gray-600 font-semibold shadow-sm hover:shadow-lg hover:scale-105 transition-all duration-200 active:scale-95"
                            onclick="showTable(false)">
                            Archive
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- Container untuk card --}}
    <div class=" px-10">
        <x-loading></x-loading>

        <div id="dataUsersProfile" class="mt-4 grid grid-cols-1 lg:grid-cols-6 gap-4"></div>
        <div id="dataUsersProfileArchive" class="hidden mt-4 grid grid-cols-1 lg:grid-cols-6 gap-4"></div>

        {{-- Pagination untuk AKTIF --}}
        <div id="paginationActive" class="flex justify-between px-6 pb-4 items-center mt-4">
            <div id="pageInfo" class="text-sm text-gray-600 dark:text-gray-300"></div>

            <div class="flex items-center gap-4">
                <select id="perPage" onchange="loadDataPaginate(1, true)" class="border py-1 px-5 rounded-full">
                    <option value="6" selected>6</option>
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>

                <div class="flex gap-2">
                    <button id="prevBtn" onclick="prevPage()"
                        class="bg-indigo-600 bg px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                        <i class='text-white bx bx-arrow-big-left-line font-medium text-lg'></i>
                    </button>
                    <button id="nextBtn" onclick="nextPage()"
                        class="bg-indigo-600 bg px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                        <i class='text-white bx bx-arrow-big-right-line font-medium  text-lg'></i>
                    </button>
                </div>
            </div>
        </div>


        {{-- Pagination uruk arsip --}}
        <div id="paginationArchive" class="hidden flex justify-between px-6 pb-4 items-center mt-4">
        <div id="pageInfoArchive" class="text-sm text-gray-600 dark:text-gray-300"></div>

            <div class="flex items-center gap-4">
                <select id="perPageArchive" onchange="loadDataPaginate(1, false)" class="border py-1 px-5 rounded-full">
                    <option value="6" selected>6</option>
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>

                <div class="flex gap-2">
                    <button id="prevBtnArchive" onclick="prevPageArchive()"
                        class="bg-indigo-600 bg px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                        <i class='text-white bx bx-arrow-big-left-line font-medium text-lg'></i>
                    </button>
                    <button id="nextBtnArchive" onclick="nextPageArchive()"
                        class="bg-indigo-600 bg px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                        <i class='text-white bx bx-arrow-big-right-line font-medium  text-lg'></i>
                    </button>
                </div>
            </div>
        </div>


        {{-- Modal EDIT --}}
        <x-modal name="edit-user-profile">
            <div class="p-6">
                <form onsubmit="event.preventDefault(); updateUsersProfile()">
                    <h2 class="text-xl shadow-md p-4 rounded-md font-bold mb-4">Edit User Profile</h2>

                    <x-text-input type="hidden" id="edit-id" />

                    <div class="space-y-3">
                        <x-input-label>New Nickname</x-input-label>
                        <x-text-input id="edit-nickname" type="text" placeholder="Name"
                            class="border p-2 w-full rounded" />

                        <x-input-label>Email</x-input-label>
                        <x-text-input id="edit-email" type="email" readonly
                            class="border p-2 w-full bg-gray-100 rounded" />

                        <x-input-label>New Phone Number</x-input-label>
                        <x-text-input id="edit-phone" type="text" placeholder="Phone Number"
                            class="border p-2 w-full rounded" />

                        <x-input-label>New Address</x-input-label>
                        <x-text-input id="edit-address" type="text" placeholder="Address"
                            class="border p-2 w-full rounded" />

                        <x-input-label class="italic">Role</x-input-label>
                        <x-text-input id="edit-role" name="user_id" class="bg-gray-100 capitalize" readonly></x-text-input>
                
                    </div>

                    <div class="flex justify-end mt-4">
                        <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                        <x-primary-button class="ml-2">Update</x-primary-button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>

    {{-- script --}}
    <script>
        window.currentUserRole = "{{ Auth::user()->role }}";

        function showTable(isActive) {
            const containerActive = document.getElementById("dataUsersProfile");
            const containerArchive = document.getElementById("dataUsersProfileArchive");
            const btnActive = document.getElementById("btnActive");
            const btnArchive = document.getElementById("btnArchive");
            const paginationActive = document.getElementById("paginationActive");
            const paginationArchive = document.getElementById("paginationArchive");

            if (isActive) {
                // Tampilkan tabel & pagination aktif
                containerActive.classList.remove("hidden");
                containerArchive.classList.add("hidden");
                paginationActive.classList.remove("hidden");
                paginationArchive.classList.add("hidden");

                // Style button
                btnActive.classList.replace("bg-gray-300", "bg-blue-500");
                btnActive.classList.replace("text-gray-600", "text-white");
                btnArchive.classList.replace("bg-blue-500", "bg-gray-300");
                btnArchive.classList.replace("text-white", "text-gray-600");
            } else {
                // Tampilkan tabel & pagination arsip
                containerActive.classList.add("hidden");
                containerArchive.classList.remove("hidden");
                paginationActive.classList.add("hidden");
                paginationArchive.classList.remove("hidden");

                // Style button
                btnArchive.classList.replace("bg-gray-300", "bg-blue-500");
                btnArchive.classList.replace("text-gray-600", "text-white");
                btnActive.classList.replace("bg-blue-500", "bg-gray-300");
                btnActive.classList.replace("text-white", "text-gray-600");
            }

        }
    </script>

    <script src="{{ asset('js/usersProfile/usersProfile.js') }}"></script>
</x-app-layout>
