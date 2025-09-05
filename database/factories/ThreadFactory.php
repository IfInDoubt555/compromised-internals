<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Thread>
 */
class ThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'board_id'        => Board::factory(),  // Creates a related Board instance
            'user_id'         => User::factory(),   // Creates a related User instance
            'title'           => $this->faker->sentence(), // Random sentence as thread title
            'slug'            => $this->faker->slug(),     // Random slug
            'body'            => $this->faker->paragraph(), // Random body content for the thread
            'status'          => $this->faker->randomElement(['draft', 'scheduled', 'published']), // Random status
            'scheduled_for'   => $this->faker->dateTimeBetween('now', '+1 month'), // Random future scheduled date
            'published_at'    => $this->faker->dateTimeBetween('-1 month', 'now'), // Random past date
            'last_activity_at'=> $this->faker->dateTimeBetween('-1 month', 'now'), // Random activity date
        ];
    }
}