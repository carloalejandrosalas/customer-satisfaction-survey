<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Category::query()->exists()) {
            Category::factory()->count(5)->create();
        }

        Product::factory()
            ->count(30)
            ->state(fn () => ['category_id' => Category::query()->inRandomOrder()->value('id')])
            ->create();
    }
}
