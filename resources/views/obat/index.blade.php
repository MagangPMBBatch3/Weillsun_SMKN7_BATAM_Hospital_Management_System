<x-app-layout>
    <x-slot name="header">
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-gray-900/60 rounded-2xl p-5 shadow-md border border-gray-200/50 dark:border-gray-700/50">

            <!-- Title -->
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-800 dark:text-gray-100 flex items-center gap-3">
                <i class='bx bx-pill text-3xl text-blue-500'></i>
                <span class=" tracking-wider">Medicine</span>
            </h2>

            <!-- Search & Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full lg:w-auto">

                <!-- Search Bar -->
                <div class="relative w-full sm:w-72">
                    <input type="text" id="search" placeholder="Search..."
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-sm px-4 py-2.5 pl-9 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-200"
                        oninput="searchObat()">
                    <i class='bx bx-search absolute left-3 top-3 h-5 w-5 text-gray-600 '></i>
                </div>

                <!-- Tombol New Medicine -->
                @if (auth()->user()->role === 'admin')
                    <x-primary-button x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'create-obat')"
                        class="flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Medicine
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
            <x-table id="tableActive" :headers="['ID', 'Medicine Name', 'type', 'stock', 'price', 'markup percent', 'selling price']" requireRole="admin">
                <tbody id="dataObatAktif"></tbody>
            </x-table>

            {{-- Tabel Data Arsip --}}
            <x-table id="tableArchive" class="hidden" :headers="['ID', 'Medicine Name', 'type', 'stock', 'price', 'markup percent', 'selling price']" requireRole="admin">
                <tbody id="dataObatArsip"></tbody>
            </x-table>

            {{-- Pagination untuk AKTIF --}}
            <x-pagination-active></x-pagination-active>

            {{-- Pagination untuk ARSIP --}}
            <x-pagination-archive></x-pagination-archive>

            {{-- Modal CREATE --}}
            <x-modal name="create-obat" focusable>
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); createObat()">
                        <h2 class="text-xl shadow-md p-4 rounded-md font-bold mb-4">Add New Medicine</h2>

                        <div class="flex justify-between gap-4">
                            <div class="w-full">
                                <x-input-label>Medicine Name</x-input-label>
                                <x-text-input id="create-nama_obat" type="text"
                                    placeholder="Enter Medicine Name..." class="border p-2 w-full rounded mb-3"
                                    required />

                                <x-input-label>Medicine Type</x-input-label>
                                <x-text-input id="create-jenis_obat" type="text"
                                    placeholder="Enter Medicine Type..." class="border p-2 w-full rounded mb-3"
                                    required />

                                <x-input-label>Stock</x-input-label>
                                <x-text-input id="create-stok" type="text" placeholder="Enter Stock..."
                                    class="border p-2 w-full rounded mb-3" required />
                            </div>

                            <div class="w-full">
                                <x-input-label>Price</x-input-label>
                                <x-text-input id="create-harga" type="text" placeholder="Enter Markup..."
                                    class="border p-2 w-full rounded mb-3" required />

                                <x-input-label>Markup Percent</x-input-label>
                                <x-text-input id="create-markup_persen" type="text"
                                    placeholder="Enter Markup (1-100)..." class="border p-2 w-full rounded mb-3"
                                    required />

                                <x-input-label>Selling Price</x-input-label>
                                <x-text-input id="create-harga_jual" type="text" placeholder="0" readonly
                                    class="border-2 border-green-600 p-2 w-full rounded mb-3" required />
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                            <x-primary-button class="ml-2">Save</x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>

            {{-- Modal EDIT --}}
            <x-modal name="edit-obat">
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); updateObat()">
                        <h2 class="text-xl shadow-md p-4 rounded-md font-bold mb-4">Edit Medicine</h2>

                        <x-text-input type="hidden" id="edit-id" />

                        <div class="flex justify-between gap-4">
                            <div class="w-full">
                                <x-input-label>New Medicine Name</x-input-label>
                                <x-text-input id="edit-nama_obat" type="text"
                                    placeholder="Enter Medicine Name..." class="border p-2 w-full rounded mb-3"
                                    required />

                                <x-input-label>New Medicine Type</x-input-label>
                                <x-text-input id="edit-jenis_obat" type="text"
                                    placeholder="Enter Medicine Type..." class="border p-2 w-full rounded mb-3"
                                    required />

                                <x-input-label>New Stock</x-input-label>
                                <x-text-input id="edit-stok" type="text" placeholder="Enter Stock..."
                                    class="border p-2 w-full rounded mb-3" required />
                            </div>

                            <div class="w-full">
                                <x-input-label>Price</x-input-label>
                                <x-text-input id="edit-harga" type="text" placeholder="Enter Price..."
                                    class="border p-2 w-full rounded mb-3" min="0" step="100" required />

                                <x-input-label>New Markup Percent</x-input-label>
                                <x-text-input id="edit-markup_persen" type="text"
                                    placeholder="Enter Markup (1-100)..." class="border p-2 w-full rounded mb-3" required />

                                <x-input-label>New Harga Jual</x-input-label>
                                <x-text-input id="edit-harga_jual" type="text" placeholder="0"
                                    class="border-2 border-green-600 p-2 w-full rounded mb-3" required />
                            </div>
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
    <script src="{{ asset('js/obat/obat.js') }}"></script>
</x-app-layout>
