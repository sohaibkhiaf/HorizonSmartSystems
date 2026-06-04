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

        User::factory()->create([
            'name' => 'Sohaib KHIAF',
            'email' => 'sohaibkhiaf@gmail.com',
            'role' => 'ADMIN',
        ]);
        User::factory()->create([
            'name' => 'Mohamed Amine SALEM',
            'email' => 'salemamine@gmail.com',
            'role' => 'OPERATOR',
        ]);
        
    }
}
