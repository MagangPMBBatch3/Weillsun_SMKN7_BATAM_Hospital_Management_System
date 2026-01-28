<?php

namespace Database\Seeders;

use App\Models\Poli\Poli;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PoliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
             [
                'nama_poli' => 'Poli Umum',
                'deskripsi' => 'Pelayanan pemeriksaan kesehatan umum',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_poli' => 'Poli Gigi',
                'deskripsi' => 'Pelayanan kesehatan gigi dan mulut',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_poli' => 'Poli Anak',
                'deskripsi' => 'Pelayanan kesehatan khusus anak',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_poli' => 'Poli Kandungan',
                'deskripsi' => 'Pelayanan kesehatan ibu dan kandungan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_poli' => 'Poli Penyakit Dalam',
                'deskripsi' => 'Pelayanan penyakit organ dalam',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_poli' => 'Poli Mata',
                'deskripsi' => 'Pelayanan kesehatan mata',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_poli' => 'Poli THT',
                'deskripsi' => 'Pelayanan telinga, hidung, dan tenggorokan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_poli' => 'Poli Saraf',
                'deskripsi' => 'Pelayanan gangguan sistem saraf',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Poli::insert($data);
    }
}
