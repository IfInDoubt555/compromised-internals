<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Use the User factory for user_id
            'post_id' => Post::factory(), // Use the Post factory for post_id
            'body' => $this->faker->paragraph,
        ];
    }
}