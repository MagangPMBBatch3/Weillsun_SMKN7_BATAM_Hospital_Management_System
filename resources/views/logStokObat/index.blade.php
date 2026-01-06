<x-app-layout>
    <x-slot name="header">
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-gray-900/60 rounded-2xl p-5 shadow-md border border-gray-200/50 dark:border-gray-700/50">

            <!-- Title -->
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-800 dark:text-gray-100 flex items-center gap-3">
                <i class='bx bx-note text-3xl text-blue-500'></i>
                <span class=" tracking-wider">Medicine Stok Log</span>
            </h2>

        </div>
    </x-slot>


    <div class="px-4 sm:px-6 lg:px-8 mb-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">

            {{-- <x-loading></x-loading> --}}

            {{-- Tabel Data Aktif --}}
            <x-table id="tableActive" :headers="[
                'medicine',
                'type',
                'Unit',
                'Stok before',
                'stok after',
                'supplier',
                'transaction date',
                'Date time',
            ]" requireRole=false>
                <tbody>

                    @if ($logs->isEmpty())
                        <tr>
                            <td colspan="9"
                                class="px-6 py-4 font-bold whitespace-nowrap text-lg text-gray-500 dark:text-gray-400 text-center">
                                No medicine stok log data available.
                            </td>

                        </tr>
                    @endif

                    @foreach ($logs as $log)
                        <tr class="text-center">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-md font-semibold text-blue-500 dark:text-blue-100">
                                {{ $log->obat->nama_obat }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap font-bold text-sm text-gray-900 dark:text-gray-100">
                                {{ $log->jenis === 'MASUK' ? 'Stock In' : '-' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $log->jumlah }}
                            </td>

                            <td
                                class="px-6 py-4 whitespace-nowrap text-md font-semibold text-red-600 dark:text-red-300">
                                {{ number_format($log->stok_sebelum, 0, ',', '.') }}
                            </td>
                            
                            <td
                                class="px-6 py-4 whitespace-nowrap text-md font-semibold text-green-600 dark:text-green-300">
                                {{ number_format($log->stok_sesudah, 0, ',', '.') }}
                            </td>
                            
                            <td
                                class="px-6 py-4 whitespace-nowrap text-md font-semibold text-purple-600 dark:text-purple-300">
                                {{ $log->pembelianObat->supplier->nama_supplier }}
                            </td>

                            <td
                                class="px-6 py-4 whitespace-nowrap text-md font-semibold text-purple-600 dark:text-purple-300">
                                {{ $log->pembelianObat->tanggal }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $log->created_at->format('d M Y, H:i') }}
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
