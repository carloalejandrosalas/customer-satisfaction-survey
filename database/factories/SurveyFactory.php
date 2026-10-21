<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Survey>
 */
class SurveyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'creator_id' => User::factory(),
            'category_id' => Category::factory(),
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];
    }
}
