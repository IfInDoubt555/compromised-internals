<?php

namespace Database\Factories;

use App\Models\RallyEventDay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RallyEventDay>
 */
class RallyEventDayFactory extends Factory
{
    /**
     * The model that this factory is for.
     *
     * @var class-string<\App\Models\RallyEventDay>
     */
    protected $model = RallyEventDay::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company, // Generate a random rally event name
            'slug' => $this->faker->slug,    // Generate a random slug for the rally event
            'location' => $this->faker->city, // Random city for event location
            'start_date' => $this->faker->dateTimeThisYear, // Random start date
            'end_date' => $this->faker->dateTimeThisYear,   // Random end date
            'description' => $this->faker->paragraph, // Random event description
            'championship' => $this->faker->word, // Random championship name
            'map_embed_url' => $this->faker->url, // Random map embed URL
            'official_url' => $this->faker->url, // Random official URL
        ];
    }
}