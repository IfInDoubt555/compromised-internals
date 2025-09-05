<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(5);

        // Avoid nullsafe on LHS of ??; value('id') returns scalar|null
        $randomUserId = User::query()->inRandomOrder()->value('id');

        return [
            'title'      => $title,
            // allow scalar id OR factory for relationship fallback
            'user_id'    => $randomUserId ?? User::factory(),
            'slug'       => Str::slug($title),
            'summary'    => $this->faker->sentence(12),
            'body'       => $this->faker->paragraphs(3, true),
            'image_path' => null,
            'status'     => $this->faker->randomElement(['pending', 'approved']),
        ];
    }
}