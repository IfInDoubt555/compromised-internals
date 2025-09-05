<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,                       // Generate a random name
            'email' => $this->faker->unique()->safeEmail,       // Generate a unique, safe email address
            'message' => $this->faker->paragraph,               // Generate a random message (paragraph)
            'created_at' => $this->faker->dateTimeThisYear,     // Generate a random created_at timestamp
            'updated_at' => $this->faker->dateTimeThisYear,     // Generate a random updated_at timestamp
        ];
    }
}