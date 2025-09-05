<?php

namespace Database\Factories;

use App\Models\RallyEvent;
use App\Models\RallyEventDay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RallyEventDay>
 */
class RallyEventDayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rally_event_id' => RallyEvent::factory(), // Generate RallyEvent using its factory
            'date' => $this->faker->date(), // Generate a random date
            'label' => $this->faker->word(), // Random word for label
            'name' => $this->faker->word(), // Random name for the day
        ];
    }
}