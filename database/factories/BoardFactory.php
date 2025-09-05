<?php

namespace Database\Factories;

use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Board>
 */
class BoardFactory extends Factory
{
    protected $model = Board::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,  // Random name for the board
            'slug' => $this->faker->slug,  // Random slug for the board
            'description' => $this->faker->sentence,  // Random description
            'created_at' => $this->faker->dateTimeThisYear,  // Random created date
            'updated_at' => $this->faker->dateTimeThisYear,  // Random updated date
        ];
    }
}