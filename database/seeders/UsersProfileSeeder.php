<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\UsersProfile\UsersProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        UsersProfile::insert([
            [
                'user_id' => 1,
                'nickname' => 'Admin',
                'telepon' => '081234567890',
                'alamat' => 'Kantor Pusat',
                'foto' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 2,
                'nickname' => 'Reception',
                'telepon' => '081234567891',
                'alamat' => 'Front Office',
                'foto' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 3,
                'nickname' => 'Cashier',
                'telepon' => '081234567893',
                'alamat' => 'Kasir',
                'foto' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 4,
                'nickname' => 'Doctor 1',
                'telepon' => '081234567892',
                'alamat' => 'Ruang Dokter',
                'foto' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 5,
                'nickname' => 'Doctor 2',
                'telepon' => '081234567892',
                'alamat' => 'Ruang Dokter',
                'foto' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => 6,
                'nickname' => 'Doctor 3',
                'telepon' => '081234567892',
                'alamat' => 'Ruang Dokter',
                'foto' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],

        ]);
    }
}
