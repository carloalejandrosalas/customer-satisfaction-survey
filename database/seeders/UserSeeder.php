<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate Technicians, Operators, and Supervisor users.
        User::factory()
            ->count(50)->create();
    }
}
