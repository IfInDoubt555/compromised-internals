<?php

namespace Database\Factories;

use App\Models\RallyStage;
use App\Models\RallyEventDay;
use Illuminate\Database\Eloquent\Factories\Factory;

class RallyStageFactory extends Factory
{
    protected $model = RallyStage::class;

    public function definition(): array
    {
        return [
            'rally_event_day_id' => RallyEventDay::factory(),
            'name' => $this->faker->word,
            'ss_number' => $this->faker->randomNumber(),
            'distance_km' => $this->faker->randomFloat(2, 1, 100),
            'start_time_local' => $this->faker->dateTimeThisYear,
        ];
    }
}