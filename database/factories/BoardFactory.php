<?php

namespace Database\Factories;

use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardFactory extends Factory
{
    protected $model = Board::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'position' => $this->faker->numberBetween(1, 100),
            'is_public' => $this->faker->boolean,
            'description' => $this->faker->sentence,
        ];
    }
}