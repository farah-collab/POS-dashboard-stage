<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create 1 admin
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@pos.com',
        ]);

        // Create 1 cashier
        User::factory()->cashier()->create([
            'name' => 'Cashier User',
            'email' => 'cashier@pos.com',
        ]);

        // Optional: Create 3 random users
        User::factory()->count(3)->create();
    }
}