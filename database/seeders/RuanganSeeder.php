<?php

namespace Database\Seeders;

use App\Models\Ruangan\Ruangan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'nama_ruangan' => 'Ruang Mawar',
                'kapasitas' => 2,
                'tarif_per_hari' => 250000,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_ruangan' => 'Ruang Melati',
                'kapasitas' => 4,
                'tarif_per_hari' => 200000,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_ruangan' => 'Ruang Anggrek',
                'kapasitas' => 1,
                'tarif_per_hari' => 350000,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_ruangan' => 'Ruang Kenanga',
                'kapasitas' => 3,
                'tarif_per_hari' => 225000,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_ruangan' => 'Ruang Flamboyan',
                'kapasitas' => 2,
                'tarif_per_hari' => 300000,
                'status' => 'tersedia',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Ruangan::insert($data);
    }
}
