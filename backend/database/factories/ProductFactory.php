<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),  // Random 3-word product name
            'category_id' => Category::inRandomOrder()->first()->id ?? 1,  // Random category
            'price' => fake()->randomFloat(2, 5, 500),  // Random price between 5.00 and 500.00
            'stock_quantity' => fake()->numberBetween(0, 100),  // Random stock 0-100
            'image' => null, 
            'qr_code' => null, 
        ];
            
        
    }
}
