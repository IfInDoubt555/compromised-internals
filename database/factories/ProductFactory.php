<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
            'name' => $this->faker->word,                          // Generate a random product name (single word)
            'description' => $this->faker->paragraph,               // Generate a random product description (paragraph)
            'price' => $this->faker->randomFloat(2, 10, 1000),      // Generate a random price between 10 and 1000 (with 2 decimals)
            'category' => $this->faker->word,                       // Generate a random product category
            'stock_quantity' => $this->faker->numberBetween(1, 100), // Generate a random stock quantity between 1 and 100
            'created_at' => $this->faker->dateTimeThisYear,         // Generate a random created_at timestamp this year
            'updated_at' => $this->faker->dateTimeThisYear,         // Generate a random updated_at timestamp this year
        ];
    }
}