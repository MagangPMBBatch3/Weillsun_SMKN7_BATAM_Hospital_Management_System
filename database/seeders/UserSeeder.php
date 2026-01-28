<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

         User::insert([
            // 1 Admin
            [
                'name' => 'Admin',
                'email' => 'weillsunfoo1@gmail.com',
                'role' => 'admin',
                'password' => Hash::make('00000000'),
                'email_verified_at' => $now,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // 2 Receptionist
            [
                'name' => 'Receptionist',
                'email' => 'receptionist@gmail.com',
                'role' => 'receptionist',
                'password' => Hash::make('receptionist123'),
                'email_verified_at' => $now,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // 3 Cashier
            [
                'name' => 'Cashier',
                'email' => 'cashier@gmail.com',
                'role' => 'cashier',
                'password' => Hash::make('cashier123'),
                'email_verified_at' => $now,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // 4 Doctor 
            [
                'name' => 'Doctor 1',
                'email' => 'doctor1@gmail.com',
                'role' => 'doctor',
                'password' => Hash::make('doctor123'),
                'email_verified_at' => $now,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // 5 Doctor
            [
                'name' => 'Doctor 2',
                'email' => 'doctor2@gmail.com',
                'role' => 'doctor',
                'password' => Hash::make('doctor123'),
                'email_verified_at' => $now,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // 6 Doctor
            [
                'name' => 'Doctor 3',
                'email' => 'doctor3@gmail.com',
                'role' => 'doctor',
                'password' => Hash::make('doctor123'),
                'email_verified_at' => $now,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
