<?php

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'            => $this->faker->randomDigitNotNull(),  // Assuming the user ID exists in the User model
            'display_name'       => $this->faker->name(),
            'location'           => $this->faker->city(),
            'rally_role'         => $this->faker->word(),
            'rally_fan_since'    => $this->faker->year(),
            'birthday'           => $this->faker->date(),
            'bio'                => $this->faker->paragraph(),
            'favorite_driver'    => $this->faker->name(),
            'favorite_car'       => $this->faker->word(),
            'favorite_event'     => $this->faker->word(),
            'favorite_game'      => $this->faker->word(),
            'car_setup_notes'    => $this->faker->sentence(),
            'website'            => $this->faker->url(),
            'instagram'          => $this->faker->url(),
            'youtube'            => $this->faker->url(),
            'twitter'            => $this->faker->url(),
            'twitch'             => $this->faker->url(),
            'profile_color'      => $this->faker->hexColor(),
            'layout_style'       => $this->faker->word(),
            'banner_image'       => $this->faker->imageUrl(),
            'show_birthday'      => $this->faker->boolean(),
            'show_car_setup_notes' => $this->faker->boolean(),
            'show_favorites'     => $this->faker->boolean(),
            'show_location'      => $this->faker->boolean(),
            'show_socials'       => $this->faker->boolean(),
        ];
    }
}