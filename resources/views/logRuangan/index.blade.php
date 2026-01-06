<x-app-layout>
    <x-slot name="header">
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-gray-900/60 rounded-2xl p-5 shadow-md border border-gray-200/50 dark:border-gray-700/50">

            <!-- Title -->
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-800 dark:text-gray-100 flex items-center gap-3">
                <i class='bx bx-note text-3xl text-blue-500'></i>
                <span class=" tracking-wider">Room Log</span>
            </h2>

        </div>
    </x-slot>


    <div class="px-4 sm:px-6 lg:px-8 mb-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">

            {{-- <x-loading></x-loading> --}}

            {{-- Tabel Data Aktif --}}
            <x-table id="tableActive" :headers="['Room', 'Entry', 'Quit', 'Patient', 'status before', 'status after', 'action', 'Date time']" requireRole=false>
                <tbody>

                    @if ($logs->isEmpty())
                        <tr>
                            <td colspan="8" class="px-6 py-4 font-bold whitespace-nowrap text-lg text-gray-500 dark:text-gray-400 text-center">
                                No room log data available.
                            </td>
                        </tr>
                    @endif
                    
                    @foreach ($logs as $log)
                        <tr class="text-center">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-md font-semibold text-blue-500 dark:text-blue-100">
                                {{ $log->ruangan->nama_ruangan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $log->rawatInap->tanggal_masuk->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $log->rawatInap->tanggal_keluar ? $log->rawatInap->tanggal_keluar->format('d M Y') : '-' }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-md font-semibold text-sky-600 dark:text-sky-300">
                                {{ $log->pasien->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <span
                                    class="{{ $log->status_sebelum === 'tersedia' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ucfirst($log->status_sebelum == 'tersedia' ? 'Available' : 'Not Available') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <span
                                    class="{{ $log->status_sesudah === 'tersedia' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ucfirst($log->status_sesudah == 'tersedia' ? 'Available' : 'Not Available') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                
                                    {{ match ($log->aksi) {
                                        'MASUK' => 'Entry',
                                        'PULANG' => 'Exit',
                                        'PINDAH_MASUK' => 'Move In',
                                        'PINDAH_KELUAR' => 'Move Out',
                                        default => 'Restore',
                                    } }}
                                
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $log->waktu->format('d M Y, H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </x-table>

            <div class=" px-4 py-3">
                {{ $logs->links() }}
            </div>

        </div>
    </div>

</x-app-layout>
