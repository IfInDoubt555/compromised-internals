<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Default API rate limiter (60 requests per minute per IP)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Login rate limiter (5 attempts per minute per email/IP combo)
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        // Bind 'message' route parameter to ContactMessage model
        Route::model('message', \App\Models\ContactMessage::class);

        // Load admin routes with web + auth + admin gate
        Route::middleware(['web', 'auth', 'can:access-admin'])
            ->prefix('admin')
            ->as('admin.')
            ->group(base_path('routes/admin.php'));
    }
}