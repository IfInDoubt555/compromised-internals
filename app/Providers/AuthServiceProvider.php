<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Comment;
use App\Policies\PostPolicy;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Post::class => PostPolicy::class,
        Comment::class => CommentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access-admin', function ($user) {
            return $user->isAdmin(); // Admin check
        });
    }
}