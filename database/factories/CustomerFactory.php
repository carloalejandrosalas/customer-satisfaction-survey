<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'state' => fake()->state(),
            'city' => fake()->city(),
            'residence_address' => fake()->streetAddress(),
            'phone_number' => fake()->unique()->numerify('##########'),
            'email_address' => fake()->unique()->safeEmail(),
            'status' => true,
        ];
    }
}
