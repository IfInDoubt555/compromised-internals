<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Thread;
use App\Models\Board;
use App\Models\Reply;
use App\Models\Order;
use App\Models\Product;
use App\Models\ContactMessage;
use App\Policies\PostPolicy;
use App\Policies\CommentPolicy;
use App\Policies\ThreadPolicy;
use App\Policies\ReplyPolicy;
use App\Policies\ContactMessagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\BoardPolicy;
use App\Policies\ProductPolicy;
use App\Policies\OrderPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class            => PostPolicy::class,
        Comment::class         => CommentPolicy::class,
        Thread::class          => ThreadPolicy::class,
        Reply::class           => ReplyPolicy::class,
        Board::class           => BoardPolicy::class,
        ContactMessage::class  => ContactMessagePolicy::class,
        Product::class         => ProductPolicy::class,
        Order::class           => OrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Global Gate for admin-only features
        Gate::define('access-admin', function ($user) {
            return $user->isAdmin();
        });
    }
}