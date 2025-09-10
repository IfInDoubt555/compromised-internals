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
+        // (CSP is handled by Spatie\Csp\AddCspHeaders in bootstrap/app.php)

        Blade::component('components.guest-layout', 'guest-layout');

        if (app()->environment('local')) {
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        }
    }
}
