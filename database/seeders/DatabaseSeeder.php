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
            'name' => 'Sohaib Khiaf',
            'email' => 'sohaibkhiaf@gmail.com',
            'is_admin' => true,
        ]);
        User::factory()->create([
            'name' => 'Salem Mohamed Amine',
            'email' => 'salemamine@gmail.com',
            'is_admin' => true,
        ]);
        
    }
}
