<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';


    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register the default API rate limiter
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Load admin routes with auth and admin gate
        Route::middleware(['web', 'auth', 'can:access-admin'])
            ->prefix('admin')
            ->as('admin.')
            ->group(base_path('routes/admin.php'));

        // Load web routes (optional here if defined in app.php via Application::configure)
        // Route::middleware('web')
        //     ->group(base_path('routes/web.php'));
    }
}
