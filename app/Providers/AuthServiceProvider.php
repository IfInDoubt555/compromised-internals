<?php

namespace App\Providers;

use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Post::class => PostPolicy::class,
    ];

    public function boot(): void
    {
        Gate::define('access-admin', function ($user) {
            return $user->isAdmin();
        });

        $this->registerPolicies(); // optional, Laravel often handles this automatically
    }
}
