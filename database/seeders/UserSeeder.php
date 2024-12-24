<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear admin
        User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'name' => 'Admin User'
        ]);

        // Crear doctor
        User::factory()->doctor()->create([
            'email' => 'doctor@example.com',
        ]);
    }
}
