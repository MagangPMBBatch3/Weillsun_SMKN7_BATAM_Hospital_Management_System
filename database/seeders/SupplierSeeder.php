<?php

namespace Database\Seeders;

use App\Models\Supplier\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $data = [
            [
                'nama_supplier' => 'PT Sehat Sentosa',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta',
                'telepon' => '0211234567',
                'email' => 'sehat.sentosa@gmail.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_supplier' => 'CV Farma Jaya',
                'alamat' => 'Jl. Sudirman No. 25, Bandung',
                'telepon' => '0227654321',
                'email' => 'farmajaya@gmail.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_supplier' => 'PT Medika Abadi',
                'alamat' => 'Jl. Diponegoro No. 5, Surabaya',
                'telepon' => '0318899776',
                'email' => 'medikaabadi@gmail.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_supplier' => 'CV Sumber Farma',
                'alamat' => 'Jl. Ahmad Yani No. 12, Medan',
                'telepon' => '0614455667',
                'email' => 'sumberfarma@gmail.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_supplier' => 'PT Global Medis',
                'alamat' => 'Jl. Gatot Subroto No. 8, Semarang',
                'telepon' => '0249988776',
                'email' => 'globalmedis@gmail.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Supplier::insert($data);
    }
}
