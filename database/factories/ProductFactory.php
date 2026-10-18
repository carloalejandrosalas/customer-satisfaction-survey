<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(3, true),
            'price' => fake()->randomFloat(2, 1, 2000),
            'image' => null,
            'description' => fake()->sentence(),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-????-####')),
            'upc' => fake()->unique()->numerify('############'),
            'status' => true,
        ];
    }
}
