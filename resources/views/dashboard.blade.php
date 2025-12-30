<x-app-layout>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 px-6 py-6 space-y-8">

    <!-- HEADER -->
    <header class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">🏥 RS Medica Dashboard</h1>
            <p class="text-gray-500 dark:text-gray-400">Selamat datang kembali, Admin 👋</p>
        </div>
        <div class="flex items-center gap-4">
            <button class="relative bg-gray-100 dark:bg-gray-800 p-3 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700">
                <i class="fa-solid fa-bell text-gray-600 dark:text-gray-300"></i>
                <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1">4</span>
            </button>
            <img src="https://ui-avatars.com/api/?name=Admin+RS" class="w-10 h-10 rounded-full border" />
        </div>
    </header>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach([
            ['title'=>'Total Pasien','icon'=>'fa-user-injured','value'=>'1,245','color'=>'blue'],
            ['title'=>'Total Dokter','icon'=>'fa-user-doctor','value'=>'85','color'=>'green'],
            ['title'=>'Ruangan Aktif','icon'=>'fa-bed','value'=>'42','color'=>'purple'],
            ['title'=>'Janji Hari Ini','icon'=>'fa-calendar-check','value'=>'16','color'=>'orange']
        ] as $card)
        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow flex items-center gap-4">
            <div class="p-4 rounded-full bg-{{ $card['color'] }}-100 text-{{ $card['color'] }}-600">
                <i class="fa-solid {{ $card['icon'] }} text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">{{ $card['title'] }}</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $card['value'] }}</h3>
            </div>
        </div>
        @endforeach
    </div>

    <!-- GRAPH SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Kunjungan Pasien (Minggu Ini)</h2>
            <canvas id="chartKunjungan"></canvas>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Ruangan Terisi vs Kosong</h2>
            
            <canvas id="chartRuangan"></canvas>
        </div>
    </div>

    <!-- TABEL PASIEN DAN DOKTER -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pasien Terbaru -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Pasien Terbaru</h2>
            <table class="w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-left">Usia</th>
                        <th class="px-4 py-2 text-left">Diagnosa</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([
                        ['nama'=>'Budi Santoso','usia'=>'34','diagnosa'=>'Demam','status'=>'Rawat Jalan'],
                        ['nama'=>'Siti Aminah','usia'=>'28','diagnosa'=>'Flu Berat','status'=>'Rawat Inap'],
                        ['nama'=>'Rudi Hartono','usia'=>'45','diagnosa'=>'Hipertensi','status'=>'Rawat Jalan']
                    ] as $p)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-2">{{ $p['nama'] }}</td>
                        <td class="px-4 py-2">{{ $p['usia'] }}</td>
                        <td class="px-4 py-2">{{ $p['diagnosa'] }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs 
                                {{ $p['status']=='Rawat Inap' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                {{ $p['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Jadwal Dokter -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Jadwal Dokter Hari Ini</h2>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach([
                    ['nama'=>'Dr. Andi Wijaya','spesialis'=>'Penyakit Dalam','jam'=>'08:00 - 12:00'],
                    ['nama'=>'Dr. Lestari Dewi','spesialis'=>'Anak','jam'=>'09:00 - 13:00'],
                    ['nama'=>'Dr. Yusuf Rahman','spesialis'=>'Bedah Umum','jam'=>'13:00 - 17:00']
                ] as $d)
                <li class="py-3 flex justify-between">
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-white">{{ $d['nama'] }}</p>
                        <p class="text-sm text-gray-500">{{ $d['spesialis'] }}</p>
                    </div>
                    <span class="text-sm text-blue-600 dark:text-blue-400">{{ $d['jam'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- BARIS BAWAH: Ruangan + Review + Aktivitas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Daftar Ruangan -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Status Ruangan</h2>
            <ul class="space-y-3">
                @foreach([
                    ['nama'=>'Ruang 101','status'=>'Terisi'],
                    ['nama'=>'Ruang 102','status'=>'Kosong'],
                    ['nama'=>'Ruang 103','status'=>'Terisi'],
                    ['nama'=>'Ruang 104','status'=>'Pembersihan']
                ] as $r)
                <li class="flex justify-between items-center">
                    <span class="text-gray-700 dark:text-gray-200">{{ $r['nama'] }}</span>
                    <span class="px-2 py-1 rounded text-xs
                        @if($r['status']=='Terisi') bg-green-100 text-green-700
                        @elseif($r['status']=='Kosong') bg-gray-100 text-gray-600
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ $r['status'] }}
                    </span>
                </li>
                @endforeach
            </ul>

            <!-- Progress Bar -->
            <div class="mt-6">
                <p class="text-sm text-gray-500 mb-1">Keterisian Rumah Sakit</p>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="bg-blue-500 h-3 rounded-full" style="width: 78%"></div>
                </div>
                <p class="text-sm text-gray-400 mt-1">78% dari total kapasitas</p>
            </div>
        </div>

        <!-- Review Pasien -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Ulasan Pasien</h2>
            <div class="space-y-4">
                @foreach([
                    ['nama'=>'Tina','ulasan'=>'Pelayanan cepat dan ramah!','rating'=>5],
                    ['nama'=>'Rizky','ulasan'=>'Dokter sangat profesional','rating'=>4],
                    ['nama'=>'Dewi','ulasan'=>'Ruang tunggu nyaman dan bersih','rating'=>5]
                ] as $u)
                <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                    <p class="font-semibold text-gray-800 dark:text-white">{{ $u['nama'] }}</p>
                    <p class="text-sm text-gray-500">{{ $u['ulasan'] }}</p>
                    <div class="text-yellow-400 mt-1">
                        @for($i=0;$i<$u['rating'];$i++)
                            ★
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Aktivitas Terakhir -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Aktivitas Terakhir</h2>
            <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                <li>🕓 08:00 — Pasien <b>Budi</b> selesai pemeriksaan</li>
                <li>🕓 09:30 — Dokter <b>Lestari</b> menambah jadwal baru</li>
                <li>🕓 11:00 — Pasien <b>Rudi</b> masuk ruang 204</li>
                <li>🕓 13:00 — Data pasien diperbarui oleh admin</li>
            </ul>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="text-center text-sm text-gray-500 dark:text-gray-400 pt-6 border-t border-gray-200 dark:border-gray-700">
        © 2025 RS Medica — Sistem Informasi Rumah Sakit. Semua hak dilindungi.
    </footer>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartKunjungan'), {
    type: 'line',
    data: {
        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        datasets: [{
            label: 'Kunjungan Pasien',
            data: [12, 19, 15, 18, 14, 10, 8],
            borderColor: '#3b82f6',
            tension: 0.4,
            fill: false
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('chartRuangan'), {
    type: 'doughnut',
    data: {
        labels: ['Terisi', 'Kosong'],
        datasets: [{
            data: [35, 10],
            backgroundColor: ['#10b981', '#f59e0b']
        }]
    },
    options: { responsive: true, cutout: '70%' }
});
</script>
</x-app-layout>
