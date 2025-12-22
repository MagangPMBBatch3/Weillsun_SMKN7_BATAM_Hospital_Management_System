<x-app-layout>
    <x-slot name="header">
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-gray-900/60 rounded-2xl p-5 shadow-md border">

            <h2 class="text-2xl font-extrabold tracking-tight flex items-center gap-3">
                <i class='bx bx-calendar text-3xl text-blue-500'></i>
                Doctors Schedule
            </h2>

            <div class="flex gap-3 w-full lg:w-auto">
                <div class="relative w-full sm:w-72">
                    <input
                        type="text"
                        id="search"
                        placeholder="Search doctor..."
                        oninput="searchJadwalTenagaMedis()"
                        class="w-full rounded-xl border px-4 py-2 pl-9 text-sm"
                    >
                    <i class='bx bx-search absolute left-3 top-3'></i>
                </div>

                @if (auth()->user()->role === 'admin')
                    <x-primary-button x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'create-kunjunganUlang')"
                        class="flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Schedule
                    </x-primary-button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

            <x-loading />

            {{-- TABLE --}}
            <x-table
                id="tableActive"
                :headers="[
                    'Doctor',
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday',
                    'Sunday'
                ]"
                requireRole=false
            >
                <tbody id="dataJadwalTenagaMedisAktif"></tbody>
            </x-table>

            <x-pagination-active />

            {{-- ================= MODAL CREATE DOCTOR ================= --}}
            <x-modal name="create-kunjunganUlang" focusable>
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); createJadwalTenagaMedis()">
                        <h2 class="text-xl font-bold mb-4">Add Doctor</h2>

                        <x-input-label>Doctor</x-input-label>
                        <select
                            id="create-dokter_id"
                            class="border p-2 w-full rounded"
                            required
                        >
                            <option value="" disabled selected>Select Doctor</option>
                            @foreach ($dokters as $d)
                                <option value="{{ $d->id }}">
                                    {{ $d->profile->nickname }}
                                </option>
                            @endforeach
                        </select>

                        <div class="flex justify-end mt-6 gap-2">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                Cancel
                            </x-secondary-button>
                            <x-primary-button>
                                Save
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>

            {{-- ================= MODAL CREATE JAM ================= --}}
            <x-modal name="create-jam" focusable>
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); createJam()">
                        <h2 class="text-xl font-bold mb-4">Add Schedule</h2>

                        {{-- Hidden --}}
                        <input type="hidden" id="jam-tenaga_medis_id">
                        <input type="hidden" id="jam-hari">

                        <div class="space-y-3">
                            <div>
                                <x-input-label>Start Time</x-input-label>
                                <x-text-input
                                    type="time"
                                    id="jam-mulai"
                                    class="w-full"
                                    required
                                />
                            </div>

                            <div>
                                <x-input-label>End Time</x-input-label>
                                <x-text-input
                                    type="time"
                                    id="jam-selesai"
                                    class="w-full"
                                    required
                                />
                            </div>
                        </div>

                        <div class="flex justify-end mt-6 gap-2">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                Cancel
                            </x-secondary-button>
                            <x-primary-button>
                                Save
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>

            {{-- ================= MODAL EDIT JAM ================= --}}
            <x-modal name="edit-kunjunganUlang" focusable>
                <div class="p-6">
                    <form onsubmit="event.preventDefault(); updateJadwalTenagaMedis()">
                        <h2 class="text-xl font-bold mb-4">Edit Schedule</h2>

                        <input type="hidden" id="edit-id">

                        <div class="space-y-3">
                            <div>
                                <x-input-label>Start Time</x-input-label>
                                <x-text-input
                                    type="time"
                                    id="edit-jam_mulai"
                                    class="w-full"
                                    required
                                />
                            </div>

                            <div>
                                <x-input-label>End Time</x-input-label>
                                <x-text-input
                                    type="time"
                                    id="edit-jam_selesai"
                                    class="w-full"
                                    required
                                />
                            </div>
                        </div>

                        <div class="flex justify-end mt-6 gap-2">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                Cancel
                            </x-secondary-button>
                            <x-primary-button>
                                Update
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>

        </div>
    </div>

    {{-- ROLE --}}
    <script>
        window.currentUserRole = "{{ Auth::user()->role }}";
    </script>

    {{-- JS --}}
    <script src="{{ asset('js/jadwalTenagaMedis/jadwalTenagaMedis.js') }}"></script>
</x-app-layout>
