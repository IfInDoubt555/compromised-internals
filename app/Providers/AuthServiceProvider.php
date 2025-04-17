<?php

namespace App\Providers;

use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Post::class, PostPolicy::class);
    }
    protected $policies = [
        Post::class => PostPolicy::class,
        \App\Models\Post::class => \App\Policies\PostPolicy::class,
    ];
    
}