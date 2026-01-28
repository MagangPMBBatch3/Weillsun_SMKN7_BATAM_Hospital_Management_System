<?php

namespace Database\Seeders;

use App\Models\Obat\Obat;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
             [
                'nama_obat' => 'Paracetamol',
                'jenis_obat' => 'Tablet',
                'stok' => 100,
                'harga' => 2000,
                'markup_persen' => 20,
                'harga_jual' => 2400,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_obat' => 'Amoxicillin',
                'jenis_obat' => 'Kapsul',
                'stok' => 100,
                'harga' => 3000,
                'markup_persen' => 25,
                'harga_jual' => 3750,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_obat' => 'OBH Combi',
                'jenis_obat' => 'Sirup',
                'stok' => 100,
                'harga' => 15000,
                'markup_persen' => 30,
                'harga_jual' => 19500,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_obat' => 'Vitamin C',
                'jenis_obat' => 'Tablet',
                'stok' => 100,
                'harga' => 1000,
                'markup_persen' => 15,
                'harga_jual' => 1150,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_obat' => 'Antasida',
                'jenis_obat' => 'Tablet',
                'stok' => 100,
                'harga' => 2500,
                'markup_persen' => 20,
                'harga_jual' => 3000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Obat::insert($data);
    }
}
