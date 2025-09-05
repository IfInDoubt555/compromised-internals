<?php

namespace Database\Factories;

use App\Models\RallyEvent;
use App\Models\RallyEventDay;
use App\Models\RallyStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RallyStage>
 */
class RallyStageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Foreign keys
            'rally_event_id' => RallyEvent::factory(), // Use RallyEventFactory to generate related event
            'rally_event_day_id' => RallyEventDay::factory(), // Use RallyEventDayFactory to generate related event day
            'second_rally_event_day_id' => RallyEventDay::factory(), // Generate a second rally event day if necessary

            // Other attributes
            'name' => $this->faker->word(), // Random name for the stage
            'ss_number' => $this->faker->unique()->numberBetween(1, 999), // Unique special stage number
            'second_ss_number' => $this->faker->unique()->numberBetween(1, 999), // Unique second special stage number (optional)
            'distance_km' => $this->faker->randomFloat(2, 1, 500), // Random distance in kilometers
            'start_time_local' => $this->faker->dateTimeThisDecade(), // Random start time for the stage
            'second_pass_time_local' => $this->faker->dateTimeThisDecade(), // Random second pass time for the stage
            'map_image_url' => $this->faker->imageUrl(), // Random map image URL
            'map_embed_url' => $this->faker->url(), // Random map embed URL
            'is_super_special' => $this->faker->boolean(), // Random boolean for super special stage
            'stage_type' => $this->faker->word(), // Random stage type (e.g., gravel, tarmac)
            'location' => $this->faker->city(), // Random location for the stage
        ];
    }
}