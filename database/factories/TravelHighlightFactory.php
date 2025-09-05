<?php

namespace Database\Factories;

use App\Models\TravelHighlight;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelHighlight>
 */
class TravelHighlightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id'      => $this->faker->randomDigitNotNull(),  // Assuming random event ID
            'title'         => $this->faker->sentence(),           // Random sentence for title
            'url'           => $this->faker->url(),                // Random URL
            'sort_order'    => $this->faker->numberBetween(1, 10),  // Random sort order
            'is_active'     => $this->faker->boolean(),            // Random boolean for is_active
            'kind'          => $this->faker->randomElement([TravelHighlight::KIND_HIGHLIGHT, TravelHighlight::KIND_TIPS]), // Random type
            'tips_md'       => $this->faker->paragraph(),          // Random markdown text for tips
            'tips_selection'=> $this->faker->randomElements([0, 1, 2, 3], $count = 2), // Random selection of tips
        ];
    }
}