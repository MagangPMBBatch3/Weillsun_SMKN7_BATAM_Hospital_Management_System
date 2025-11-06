<x-app-layout>
    <x-slot name="header">
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-gray-900/60 rounded-2xl p-5 shadow-md border border-gray-200/50 dark:border-gray-700/50">

            <!-- Title -->
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-800 dark:text-gray-100 flex items-center gap-3">
                <i class='bx bx-user text-3xl text-blue-500'></i>
                <span class=" tracking-wider">Outpatient Department</span>
            </h2>

            <!-- Search & Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full lg:w-auto">

                <!-- Search Bar -->
                <div class="relative w-full sm:w-72">
                    <input type="text" id="search" placeholder="Search..."
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-sm px-4 py-2.5 pl-9 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-200"
                        oninput="searchPoli()">
                    <i class='bx bx-search absolute left-3 top-3 h-5 w-5 text-gray-600 '></i>
                </div>

                <!-- Tombol New Patient -->
                @if (auth()->user()->role === 'admin')
                    <x-primary-button x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'create-poli')"
                        class="flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Clinic
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
            <x-table id="tableActive" :headers="['ID', 'clinic Name', 'Description']" requireRole="admin">
                <tbody id="dataPoliAktif"></tbody>
            </x-table>

            {{-- Tabel Data Arsip --}}
            <x-table id="tableArchive" class="hidden" :headers="['ID', 'clinic Name', 'Description']" requireRole="admin">
                <tbody id="dataPoliArsip"></tbody>
            </x-table>

            {{-- Pagination untuk AKTIF --}}
            <x-pagination-active></x-pagination-active>

            {{-- Pagination untuk ARSIP --}}
            <x-pagination-archive></x-pagination-archive>

            {{-- Modal CREATE --}}
            <x-modal name="create-poli" focusable>
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); createPoli()">
                        <h2 class="text-xl shadow-md p-4 rounded-md font-bold mb-4">Add New Clinic</h2>

                        <div class="space-y-3">
                            <x-input-label>Clinic Name</x-input-label>
                            <x-text-input id="create-nama_poli" type="text" placeholder="Enter Clinic Name..."
                                class="border p-2 w-full rounded" required />
                            
                            <x-input-label>Description</x-input-label>
                            <textarea id="create-deskripsi" placeholder="Enter Description..."
                                class="border p-2 mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required></textarea>
                        </div>

                        <div class="flex justify-end mt-4">
                            <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                            <x-primary-button class="ml-2">Save</x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>

            {{-- Modal EDIT --}}
            <x-modal name="edit-poli">
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); updatePoli()">
                        <h2 class="text-xl shadow-md p-4 rounded-md font-bold mb-4">Edit Clinic</h2>

                        <x-text-input type="hidden" id="edit-id" />

                        <div class="space-y-3">
                            <x-input-label>New Clinic Name</x-input-label>
                            <x-text-input id="edit-nama_poli" type="text" placeholder="Name"
                                class="border p-2 w-full rounded" />

                            <x-input-label>New Description</x-input-label>
                            <textarea id="edit-deskripsi" placeholder="Enter Description..."
                                class="border p-2 mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required></textarea>
                        </div>

                        <div class="flex justify-end mt-4">
                            <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
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
    <script src="{{ asset('js/poli/poli.js') }}"></script>
</x-app-layout>
