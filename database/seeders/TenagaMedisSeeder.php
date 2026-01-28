<?php

namespace Database\Seeders;

use App\Models\TenagaMedis\TenagaMedis;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TenagaMedisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $now = Carbon::now();

        TenagaMedis::insert([
            [
                'profile_id' => 4, // Doctor 1
                'spesialisasi' => 'Dokter Umum',
                'no_str' => 'STR-DOC-001',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'profile_id' => 5, // Doctor 2
                'spesialisasi' => 'Dokter Anak',
                'no_str' => 'STR-DOC-002',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'profile_id' => 6, // Doctor 3
                'spesialisasi' => 'Dokter Penyakit Dalam',
                'no_str' => 'STR-DOC-003',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
