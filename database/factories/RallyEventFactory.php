<?php

namespace Database\Factories;

use App\Models\RallyEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class RallyEventFactory extends Factory
{
    protected $model = RallyEvent::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'location' => $this->faker->city,
            'description' => $this->faker->sentence,
            'start_date' => $this->faker->dateTimeThisYear,
            'end_date' => $this->faker->dateTimeThisYear,
            'championship' => $this->faker->word,
        ];
    }
}