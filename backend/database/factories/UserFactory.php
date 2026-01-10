<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'password' => Hash::make('password123'),  // Everyone gets same password for testing
            'role' => fake()->randomElement(['admin', 'cashier']), // Random role
            'is_active' => true,
        ];
    }


    //fn: is like a shorter way to say function
    //state: elle prend la valeur par defaut and change it to admin or cashier
    public function admin(){
        return $this ->state(fn(array $atttributes) =>[
            'role' => 'admin',
        ]);
    }

    public function cashier(){
        return $this ->state(fn(array $atttributes) =>[
            'role' => 'cashier',
        ]);
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
}
