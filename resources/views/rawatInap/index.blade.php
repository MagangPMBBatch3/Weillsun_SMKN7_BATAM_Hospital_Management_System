<x-app-layout>
    <x-slot name="header">
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-gray-900/60 rounded-2xl p-5 shadow-md border border-gray-200/50 dark:border-gray-700/50">

            <!-- Title -->
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-800 dark:text-gray-100 flex items-center gap-3">
                <i class='bx bx-receipt text-3xl text-blue-500'></i>
                <span class=" tracking-wider">Inpatient Care</span>
            </h2>

            <!-- Search & Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full lg:w-auto">

                <!-- Search Bar -->
                <div class="relative w-full sm:w-72">
                    <input type="text" id="search" placeholder="Search..."
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-sm px-4 py-2.5 pl-9 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-200"
                        oninput="searchRawatInap()">
                    <i class='bx bx-search absolute left-3 top-3 h-5 w-5 text-gray-600 '></i>
                </div>

                <!-- Tombol New User -->
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist')
                    <x-primary-button x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'create-rawatInap')"
                        class="flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Data
                    </x-primary-button>
                @endif


                <!-- Tombol Aktif / Arsip -->
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'receptionist')
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
            <x-table id="tableActive" :headers="['ID', 'patient', 'room', 'entry date', 'exit date', 'Fee', 'status']" :requireRole="['admin','receptionist']">
                <tbody id="dataRawatInapAktif"></tbody>
            </x-table>

            {{-- Tabel Data Arsip --}}
            <x-table id="tableArchive" class="hidden" :headers="['ID', 'Patient', 'room', 'entry date', 'exit date', 'Fee', 'status']" :requireRole="['admin','receptionist']">
                <tbody id="dataRawatInapArsip"></tbody>
            </x-table>

            {{-- Pagination untuk AKTIF --}}
            <x-pagination-active></x-pagination-active>

            {{-- Pagination untuk ARSIP --}}
            <x-pagination-archive></x-pagination-archive>

            {{-- Modal CREATE --}}
            <x-modal name="create-rawatInap" focusable>
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); createRawatInap()">
                        <h2 class="text-xl shadow-md p-4 rounded-md font-bold mb-4">Add New Record</h2>

                        <div class="space-y-3">

                            <x-input-label>Patient</x-input-label>
                            <select
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                name="create-nama" id="create-nama">
                                <option value="" class="text-gray-500 italic">Select Patient</option>
                                @foreach ($pasiens as $pasien)
                                    <option value="{{ $pasien->id }}">
                                        {{ $pasien->nama }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-label>Room</x-input-label>
                            <select
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                name="create-ruangan" id="create-ruangan">
                                <option value="" class="text-gray-500 italic">Select Room</option>
                                @foreach ($ruangan as $room)
                                    <option value="{{ $room->id }}">
                                        {{ $room->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-label>Entry Date</x-input-label>
                            <x-text-input id="create-tanggal-masuk" type="date" class="border p-2 w-full rounded"
                                required />

                            <x-input-label>Exit Date</x-input-label>
                            <x-text-input id="create-tanggal-keluar" type="date" class="border p-2 w-full rounded"
                                required />

                            <x-input-label>Fee</x-input-label>
                            <x-text-input id="create-biaya-inap" type="text" placeholder="0" readonly
                                class="border-2 border-green-600 p-2 w-full rounded mb-3 bg-gray-100 font-semibold"
                                required />

                            <x-input-label>Status</x-input-label>
                            <select
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                name="create-status" id="create-status">
                                <option value="" class="text-gray-500 italic">Select Status</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Pulang">Pulang</option>
                                <option value="Pindah_Ruangan">Pindah Ruangan</option>
                            </select>

                        </div>

                        <div class="flex justify-end mt-4">
                            <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                            <x-primary-button class="ml-2">Save</x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>

            {{-- Modal EDIT --}}
            <x-modal name="edit-rawatInap">
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); updateRawatInap()">
                        <h2 class="text-xl shadow-md p-4 rounded-md font-bold mb-4">Edit Record</h2>

                        <x-text-input type="hidden" id="edit-id" />

                        <div class="space-y-3">

                            <x-input-label>New Patient</x-input-label>
                            <select
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                name="edit-nama" id="edit-nama">
                                <option value="" class="text-gray-500 italic">Select Patient</option>
                                @foreach ($Allpasiens as $pasien)
                                    <option value="{{ $pasien->id }}">
                                        {{ $pasien->nama }}
                                    </option>
                                @endforeach
                            </select>


                            <x-input-label>New Status</x-input-label>
                            <select
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                        focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600
                                        rounded-md shadow-sm"
                                name="edit-status" id="edit-status">

                                <option value="" class="text-gray-500 italic">Select Status</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Pulang">Pulang</option>
                                <option value="Pindah_Ruangan">Pindah Ruangan</option>
                            </select>


                            <x-input-label>New Room</x-input-label>
                            <select
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                                        focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600
                                        rounded-md shadow-sm disabled:cursor-not-allowed"
                                name="edit-ruangan" id="edit-ruangan" disabled>

                                <option value="" class="text-gray-500 italic">Select Room</option>
                                @foreach ($ruangan as $room)
                                    <option value="{{ $room->id }}">
                                        {{ $room->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>


                            <x-input-label>New Entry Date</x-input-label>
                            <x-text-input id="edit-tanggal-masuk" type="date" class="border p-2 w-full rounded" />

                            <x-input-label>New Exit Date</x-input-label>
                            <x-text-input id="edit-tanggal-keluar" type="date"
                                class="border p-2 w-full rounded" />

                            <x-input-label>New Fee</x-input-label>
                            <x-text-input id="edit-biaya-inap" type="text" placeholder="0" readonly
                                class="border-2 border-green-600 p-2 w-full rounded bg-gray-100 font-semibold" />


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
    <script src="{{ asset('js/rawatInap/rawatInap.js') }}"></script>
    <script></script>

</x-app-layout>
