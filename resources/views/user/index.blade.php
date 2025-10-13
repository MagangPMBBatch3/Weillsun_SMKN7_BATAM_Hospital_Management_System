<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                Users
            </h2>

            <div class="flex items-center">
                <input type="text" id="search" placeholder="Search..."
                    class="rounded-full w-48 sm:w-80 border-gray-300 shadow-sm px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    oninput="searchUser()">
            </div>

            @if (auth()->user()->role === 'admin')
                <div class="flex gap-2 mt-2">
                    <button id="btnActive" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        onclick="showTable(true)">
                        Aktif
                    </button>
                    <button id="btnArchive" class="px-4 py-2 bg-gray-300 text-black rounded hover:bg-gray-400"
                        onclick="showTable(false)">
                        Arsip
                    </button>
                </div>
            @endif

            @if (auth()->user()->role === 'admin')
                <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-user')"
                    class="transform hover:scale-105 transition-transform duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New User
                </x-primary-button>
            @endif
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">

            {{-- Loading Overlay --}}
            <div id="loadingOverlay"
                class="hidden absolute inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-[9999]">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl flex flex-col items-center">
                    <svg class="animate-spin h-10 w-10 text-blue-500 mb-3" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="text-gray-700 dark:text-gray-300 font-medium">Loading...</p>
                </div>
            </div>

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
                    <select id="perPage" onchange="loadDataPaginate(1, true)" class="border p-2 rounded">
                        <option value="5" selected>5</option>
                        <option value="10">10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>

                    <div class="flex gap-2">
                        <button id="prevBtn" onclick="prevPage()"
                            class="bg-gray-300 px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                            ⬅ Back
                        </button>
                        <button id="nextBtn" onclick="nextPage()"
                            class="bg-gray-300 px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                            Next ➡
                        </button>
                    </div>
                </div>
            </div>

            {{-- Pagination untuk ARSIP --}}
            <div id="paginationArchive" class="hidden flex justify-between px-6 pb-4 items-center mt-4">
                <div id="pageInfoArchive" class="text-sm text-gray-600 dark:text-gray-300"></div>

                <div class="flex items-center gap-4">
                    <select id="perPageArchive" onchange="loadDataPaginate(1, false)" class="border p-2 rounded">
                        <option value="5" selected>5</option>
                        <option value="10">10</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>

                    <div class="flex gap-2">
                        <button id="prevBtnArchive" onclick="prevPageArchive()"
                            class="bg-gray-300 px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                            ⬅ Back
                        </button>
                        <button id="nextBtnArchive" onclick="nextPageArchive()"
                            class="bg-gray-300 px-3 py-1 disabled:cursor-not-allowed rounded disabled:opacity-50">
                            Next ➡
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
                                <option value="admin">Admin</option>
                                <option value="receptionist">Receptionist</option>
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
                btnActive.classList.replace("text-black", "text-white");
                btnArchive.classList.replace("bg-blue-500", "bg-gray-300");
                btnArchive.classList.replace("text-white", "text-black");
            } else {
                // Tampilkan tabel & pagination arsip
                tableActive.classList.add("hidden");
                tableArchive.classList.remove("hidden");
                paginationActive.classList.add("hidden");
                paginationArchive.classList.remove("hidden");

                // Style button
                btnArchive.classList.replace("bg-gray-300", "bg-blue-500");
                btnArchive.classList.replace("text-black", "text-white");
                btnActive.classList.replace("bg-blue-500", "bg-gray-300");
                btnActive.classList.replace("text-white", "text-black");
            }
        }
    </script>
    {{-- JS --}}
    <script src="{{ asset('js/user/user.js') }}"></script>
</x-app-layout>
