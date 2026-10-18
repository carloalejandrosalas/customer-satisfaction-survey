<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Generate default admin user.
        User::factory()
            ->count(1)
            ->admin(
                env('DEFAULT_ADMIN_NAME', 'ADMIN'),
                env('DEFAULT_ADMIN_EMAIL', 'admin@localhost.com'),
                env('DEFAULT_ADMIN_PASSWORD', 'password')
            )->create();

        // Generate several users accounts first.
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
