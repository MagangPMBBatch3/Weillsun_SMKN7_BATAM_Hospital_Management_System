<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Pasien\Pasien;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $now = Carbon::now();

        $data = [
            [
                'nama' => 'Ahmad Fauzi',
                'tanggal_lahir' => '2000-01-15',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Merdeka No. 1',
                'telepon' => '081234567801',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Siti Aisyah',
                'tanggal_lahir' => '1999-03-22',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Melati No. 5',
                'telepon' => '081234567802',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Budi Santoso',
                'tanggal_lahir' => '1998-07-10',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Kenanga No. 12',
                'telepon' => '081234567803',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Dewi Lestari',
                'tanggal_lahir' => '2001-11-05',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Anggrek No. 8',
                'telepon' => '081234567804',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Pasien::insert($data);
    }
}
