<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-gray-900/60 rounded-2xl p-5 shadow-md border border-gray-200/50 dark:border-gray-700/50">

            <!-- Title -->
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-800 dark:text-gray-100 flex items-center gap-3">
                <i class='bx bx-user text-3xl text-blue-500'></i>
                <span class=" tracking-wider">Users</span>
            </h2>

            <!-- Search & Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full lg:w-auto">

                <!-- Search Bar -->
                <div class="relative w-full sm:w-72">
                    <input type="text" id="search" placeholder="Search user..."
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-sm px-4 py-2.5 pl-9 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-200"
                        oninput="searchUser()">
                    <i class='bx bx-search absolute left-3 top-3 h-5 w-5 text-gray-600 '></i>
                </div>

                <!-- Tombol New User -->
                @if (auth()->user()->role === 'admin')
                    <x-primary-button x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'create-user')"
                        class="flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New User
                    </x-primary-button>
                @endif


                <!-- Tombol Aktif / Arsip -->
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


    <div class="px-4 sm:px-6 lg:px-8 mb-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">

            <x-loading></x-loading>

            {{-- Tabel Data Aktif --}}
            <x-table id="tableActive" :headers="['ID', 'Name', 'Email', 'Role']" requireRole="admin">
                <tbody id="dataUserAktif"></tbody>
            </x-table>

            {{-- Tabel Data Arsip --}}
            <x-table id="tableArchive" class="hidden" :headers="['ID', 'Name', 'Email', 'Role']" requireRole="admin">
                <tbody id="dataUserArsip"></tbody>
            </x-table>

            {{-- Pagination untuk AKTIF --}}
            <div id="paginationActive" class="flex justify-between px-6 pb-4 items-center mt-4">
                <div id="pageInfo" class="text-sm text-gray-600 dark:text-gray-300"></div>

                <div class="flex items-center gap-4">
                    <select id="perPage" onchange="loadDataPaginate(1, true)" class="border py-1 px-5 rounded-full">
                        <option value="5" selected>5</option>
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

            {{-- Pagination untuk ARSIP --}}
            <div id="paginationArchive" class="hidden flex justify-between px-6 pb-4 items-center mt-4">
                <div id="pageInfoArchive" class="text-sm text-gray-600 dark:text-gray-300"></div>

                <div class="flex items-center gap-4">
                    <select id="perPageArchive" onchange="loadDataPaginate(1, false)" class="border py-1 px-5 rounded-full">
                        <option value="5" selected>5</option>
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

            {{-- Modal CREATE --}}
            <x-modal name="create-user" focusable>
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); createUser()">
                        <h2 class="text-lg font-bold mb-4">Tambah User</h2>

                        <div class="space-y-3">
                            <x-text-input id="create-name" type="text" placeholder="Nama"
                                class="border p-2 w-full rounded" required />
                            <x-text-input id="create-email" type="email" placeholder="Email"
                                class="border p-2 w-full rounded" required />
                            <x-text-input id="create-password" type="password" placeholder="Password"
                                class="border p-2 w-full rounded" required />
                            <select id="create-role" required class="border p-2 w-full rounded">
                                <option value="receptionist">Receptionist</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="flex justify-end mt-4">
                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                            <x-primary-button class="ml-2">Simpan</x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>

            {{-- Modal EDIT --}}
            <x-modal name="edit-user">
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); updateUser()">
                        <h2 class="text-lg font-bold mb-4">Edit User</h2>

                        <x-text-input type="hidden" id="edit-id" />

                        <div class="space-y-3">
                            <x-text-input id="edit-name" type="text" placeholder="Nama"
                                class="border p-2 w-full rounded" />
                            <x-text-input id="edit-email" type="email" placeholder="Email"
                                class="border p-2 w-full rounded" />
                            <select id="edit-role" class="border p-2 w-full rounded">
                                <option value="admin">Admin</option>
                                <option value="receptionist">Receptionist</option>
                            </select>
                        </div>

                        <div class="flex justify-end mt-4">
                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                            <x-primary-button class="ml-2">Update</x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>

        </div>
    </div>

    <script>
        window.currentUserRole = "{{ Auth::user()->role }}";

        function showTable(isActive) {
            const tableActive = document.getElementById("tableActive");
            const tableArchive = document.getElementById("tableArchive");
            const btnActive = document.getElementById("btnActive");
            const btnArchive = document.getElementById("btnArchive");
            const paginationActive = document.getElementById("paginationActive");
            const paginationArchive = document.getElementById("paginationArchive");

            if (isActive) {
                // Tampilkan tabel & pagination aktif
                tableActive.classList.remove("hidden");
                tableArchive.classList.add("hidden");
                paginationActive.classList.remove("hidden");
                paginationArchive.classList.add("hidden");

                // Style button
                btnActive.classList.replace("bg-gray-300", "bg-blue-500");
                btnActive.classList.replace("text-gray-600", "text-white");
                btnArchive.classList.replace("bg-blue-500", "bg-gray-300");
                btnArchive.classList.replace("text-white", "text-gray-600");
            } else {
                // Tampilkan tabel & pagination arsip
                tableActive.classList.add("hidden");
                tableArchive.classList.remove("hidden");
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
    {{-- JS --}}
    <script src="{{ asset('js/user/user.js') }}"></script>
</x-app-layout>
