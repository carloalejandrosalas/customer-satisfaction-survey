<?php

namespace Database\Factories;

use App\Models\User;
use App\RoleEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'state' => fake()->state(),
            'city' => fake()->city(),
            'address' => fake()->address(),
            'role' => fake()->randomElement([
                RoleEnum::OPERATOR,
                RoleEnum::SUPERVISOR,
                RoleEnum::TECHNICIAN,
            ]),
            'phone_number' => fake()->phoneNumber(),
            'change_password' => false,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(?string $name, ?string $email = null, ?string $pass = null): static
    {
        $finalPassword = $pass !== null ? $pass : 'password';

        return $this->state(fn (array $attributes) => [
            'name' => $name !== null ? $name : fake()->name(),
            'email' => $email !== null ? $email : fake()->unique()->safeEmail(),
            'password' => Hash::make($finalPassword),
            'role' => RoleEnum::ADMIN,
        ]);
    }
}
