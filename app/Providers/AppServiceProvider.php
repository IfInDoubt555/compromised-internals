<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('components.guest-layout', 'guest-layout');
    
        if (app()->environment('local')) {
            // Use the incoming request’s origin (scheme + host) as the root for generated URLs
            URL::useOrigin(request());
        }
    }
}
