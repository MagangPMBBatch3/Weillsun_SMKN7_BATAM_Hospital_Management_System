<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'weillsunfoo1@gmail.com',
            'password' => '$2y$12$w.CqEstZkdSLHe9DijRTEu4ctsnPRTq.u6HF6Vh169MYrgo1Od506',
        ]);
        
    }
}
