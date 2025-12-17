<x-app-layout>
    <x-slot name="header">
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-gray-900/60 rounded-2xl p-5 shadow-md border border-gray-200/50 dark:border-gray-700/50">

            <!-- Title -->
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-800 dark:text-gray-100 flex items-center gap-3">
                <i class='bx bx-receipt text-3xl text-blue-500'></i>
                <span class=" tracking-wider">Patient Payment Details</span>
            </h2>

            <!-- Search & Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full lg:w-auto">

                <!-- Search Bar -->
                <div class="relative w-full sm:w-72">
                    <input type="text" id="search" placeholder="Search..."
                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-sm px-4 py-2.5 pl-9 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-200"
                        oninput="searchDetailPembayaranPasien()">
                    <i class='bx bx-search absolute left-3 top-3 h-5 w-5 text-gray-600 '></i>
                </div>

                <!-- Tombol New User -->
                @if (auth()->user()->role === 'admin')
                    <x-primary-button x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'create-detailPembayaranPasien')"
                        class="flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Payment
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

            {{-- <x-table id="tableActive" :headers="['ID', 'Patient', 'type', 'Unit Price', 'amount', 'subtotal']" :showActionHeader="false">
                <tbody id="dataDetailPembayaranPasienAktif"></tbody>
            </x-table>

            <x-table id="tableArchive" class="hidden" :headers="['ID', 'Patient', 'cost type', 'Unit Price', 'amount', 'subtotal']" :showActionHeader="false">
                <tbody id="dataDetailPembayaranPasienArsip"></tbody>
            </x-table> --}}

            <!-- CARD ACTIVE -->
            <div id="cardActive" class="p-6 space-y-6"></div>

            <!-- CARD ARCHIVE -->
            <div id="cardArchive" class="p-6 space-y-6 hidden"></div>

            {{-- Pagination untuk AKTIF --}}
            <x-pagination-active></x-pagination-active>

            {{-- Pagination untuk ARSIP --}}
            <x-pagination-archive></x-pagination-archive>

            {{-- Modal CREATE --}}
            <x-modal name="create-detailPembayaranPasien" focusable>
                <div class="p-6 overflow-y-scroll max-h-[100vh]">
                    <form onsubmit="event.preventDefault(); createDetailPembayaranPasien()">
                        <h2 class="text-xl shadow-md p-4 rounded-md font-bold mb-4">Add New Prescriptions</h2>

                        <div class="space-y-3">

                            <x-input-label>Patient</x-input-label>
                            <select
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                name="create-nama" id="create-nama">
                                <option value="" class="text-gray-500 italic">Select Patient</option>
                                @foreach ($pasiens as $pasien)
                                    <option value="{{ $pasien->id }}">
                                        {{ $pasien->pasien->nama }} - {{ $pasien->tanggal_bayar }}
                                    </option>
                                @endforeach
                            </select>

                            <div id="dynamic-container" class="space-y-4">

                                <!-- ROW PERTAMA -->
                                <div
                                    class="dynamic-row bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 
                                            p-4 rounded-xl shadow-sm space-y-3 transition-all">

                                    <div>
                                        <x-input-label>Cost Type</x-input-label>
                                        <select name="create-tipe-biaya[]" id="create-tipe-biaya[]"
                                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 
                                                focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                            <option value="">Select Cost Type</option>
                                            <option value="konsultasi">Konsultasi</option>
                                            <option value="obat">Obat</option>
                                            <option value="lab">Lab</option>
                                            <option value="radiologi">Radiologi</option>
                                            <option value="rawat_inap">Rawat Inap</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </div>

                                    <div>
                                        <x-input-label>Amount</x-input-label>
                                        <input type="text" name="create-jumlah[]"
                                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 
                                                focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                                            placeholder="Enter Amount">
                                    </div>

                                    <div>
                                        <x-input-label>Unit Price</x-input-label>
                                        <input type="text" name="create-harga-satuan[]"
                                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 
                                                focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                                            placeholder="Enter Unit Price">
                                    </div>

                                    <div>
                                        <x-input-label>Subtotal</x-input-label>
                                        <input type="text" name="create-subtotal[]"
                                            class="border-2 border-green-600 py-2 px-3 w-full rounded-full mb-3 bg-gray-100 font-semibold"
                                            placeholder="0" readonly>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <button type="button"
                                class="mt-3 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl shadow-md hover:shadow-lg 
                                    transition-all duration-200 active:scale-95 flex items-center gap-2"
                                onclick="addRow()">
                                <i class='bx bx-plus text-xl'></i> New
                            </button>



                            <div class="flex justify-end mt-4">
                                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                <x-primary-button class="ml-2">Save</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </x-modal>

            {{-- Modal EDIT --}}
            <x-modal name="edit-detailPembayaranPasien">
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); updateDetailPembayaranPasien()">
                        <h2 class="text-xl shadow-md p-4 rounded-md font-bold mb-4">Edit Prescriptions</h2>

                        <x-text-input type="hidden" id="edit-id" />

                        <div class="space-y-3">
                            <x-input-label>New Patient</x-input-label>
                            <select
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                name="edit-nama" id="edit-nama">
                                <option value="" class="text-gray-500 italic">Select Patient</option>
                                @foreach ($pasiens as $pasien)
                                    <option value="{{ $pasien->id }}">
                                        {{ $pasien->pasien->nama }} - {{ $pasien->tanggal_bayar }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-label>New Cost Type</x-input-label>
                            <select id="edit-tipe-biaya"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 
                                    focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                <option value="">Select Cost Type</option>
                                <option value="konsultasi">Konsultasi</option>
                                <option value="obat">Obat</option>
                                <option value="lab">Lab</option>
                                <option value="radiologi">Radiologi</option>
                                <option value="rawat_inap">Rawat Inap</option>
                                <option value="lainnya">Lainnya</option>
                            </select>

                            <!-- Obat Dropdown (hanya tampil jika tipe_biaya = obat) -->
                            <div id="edit-obat-container" class="hidden">
                                <x-input-label>Select Obat</x-input-label>
                                <select id="edit-obat-select"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 
                                        focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                    <option value="">Select Obat</option>
                                </select>
                            </div>

                            <x-input-label>New Amount</x-input-label>
                            <x-text-input id="edit-jumlah" type="text" placeholder="Enter Amount..."
                                class="border p-2 w-full rounded" />

                            <x-input-label>New Unit Price</x-input-label>
                            <x-text-input id="edit-harga-satuan" type="text" placeholder="Enter Unit Price..."
                                class="border p-2 w-full rounded" />

                            <x-input-label>New Subtotal</x-input-label>
                            <input type="text" id="edit-subtotal"
                                class="border-2 border-green-600 py-2 px-3 w-full rounded-full mb-3 bg-gray-100 font-semibold"
                                placeholder="0" readonly>

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
        function addRow() {
            const container = document.getElementById("dynamic-container");

            const row = document.createElement("div");
            row.className =
                "dynamic-row bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 " +
                "p-4 rounded-xl shadow-sm space-y-3 transition-all transform scale-95 opacity-0";

            row.innerHTML = `
            <div>
                <label class="text-sm font-medium">Cost Type</label>
                <select name="create-tipe-biaya[]"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 
                        focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    <option value="">Select Cost Type</option>
                    <option value="konsultasi">Konsultasi</option>
                    <option value="obat">Obat</option>
                    <option value="lab">Lab</option>
                    <option value="radiologi">Radiologi</option>
                    <option value="rawat_inap">Rawat Inap</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

                <div>
                    <label class="text-sm font-medium">Amount</label>
                    <input type="text" name="create-jumlah[]"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 
                            focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                        placeholder="Enter Amount">
                </div>

                <div>
                    <label class="text-sm font-medium">Unit Price</label>
                    <input type="text" name="create-harga-satuan[]"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 
                            focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                        placeholder="Enter Unit Price">
                </div>
                
                <div>
                    <label class="text-sm font-medium">Subtotal</label>
                    <input type="text" name="create-subtotal[]"
                        class="border-2 border-green-600 py-2 px-3 w-full rounded-full mb-3 bg-gray-100 font-semibold"
                        placeholder="0" readonly>
                </div>
            `;

            container.appendChild(row);

            // Animasi masuk
            setTimeout(() => {
                row.classList.remove("scale-95", "opacity-0");
                row.classList.add("scale-100", "opacity-100");
            }, 10);
        }

        window.currentUserRole = "{{ Auth::user()->role }}";

        function showTable(isActive) {
            const cardActive = document.getElementById("cardActive");
            const cardArchive = document.getElementById("cardArchive");
            const btnActive = document.getElementById("btnActive");
            const btnArchive = document.getElementById("btnArchive");
            const paginationActive = document.getElementById("paginationActive");
            const paginationArchive = document.getElementById("paginationArchive");

            if (isActive) {
                // Tampilkan tabel & pagination aktif
                cardActive.classList.remove("hidden");
                cardArchive.classList.add("hidden");
                paginationActive.classList.remove("hidden");
                paginationArchive.classList.add("hidden");

                // Style button
                btnActive.classList.replace("bg-gray-300", "bg-blue-500");
                btnActive.classList.replace("text-gray-600", "text-white");
                btnArchive.classList.replace("bg-blue-500", "bg-gray-300");
                btnArchive.classList.replace("text-white", "text-gray-600");
            } else {
                // Tampilkan tabel & pagination arsip
                cardActive.classList.add("hidden");
                cardArchive.classList.remove("hidden");
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
    <script src="{{ asset('js/detailPembayaranPasien/detailPembayaranPasien.js') }}"></script>
    <script></script>

</x-app-layout>
