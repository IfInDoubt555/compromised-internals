<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';


    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Load public web routes
        // Route::middleware('web')
        //     ->group(base_path('routes/web.php'));

        // Load admin routes (auth + admin-only)
        Route::middleware(['web', 'auth', 'can:access-admin'])
            ->prefix('admin')
            ->as('admin.')
            ->group(base_path('routes/admin.php'));
    }
}
