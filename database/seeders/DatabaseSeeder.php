<?php

namespace Database\Seeders;

use App\Models\Ruangan\Ruangan;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            UsersProfileSeeder::class,
            TenagaMedisSeeder::class,
            PasienSeeder::class,
            ObatSeeder::class,
            PoliSeeder::class,
            RuanganSeeder::class,
            SupplierSeeder::class
        ]);
    }
}
