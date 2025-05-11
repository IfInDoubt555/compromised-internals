<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(5);
        return [
            'title' => $title,
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(), 
            'slug' => Str::slug($title),
            'summary' => $this->faker->sentence(12),
            'body' => $this->faker->paragraphs(3, true),
            'image_path' => null,
            'status' => $this->faker->randomElement(['pending', 'approved']),
        ];
    }
}