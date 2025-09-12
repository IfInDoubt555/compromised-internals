<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

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
        // (CSP is handled by Spatie\Csp\AddCspHeaders in bootstrap/app.php)

        Blade::component('components.guest-layout', 'guest-layout');

        if (app()->environment('local')) {
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        } else {
            // Behind Cloudflare/HTTPS: keep signed URLs valid by locking scheme
            URL::forceScheme('https');
        }

        /**
         * ---- Named Rate Limiters ----
         * Central place to tune auth / UGC throttles.
         */

        // Registration: 5 attempts/min per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Login: 10/min keyed by email+IP
        RateLimiter::for('login', function (Request $request) {
            $key = strtolower($request->input('email', '')) . '|' . $request->ip();
            return Limit::perMinute(10)->by($key);
        });

        // Password reset email request
        RateLimiter::for('password.email', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perHour(12)->by($request->ip()),
            ];
        });

        // Password reset submit
        RateLimiter::for('password.reset', function (Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });

        // Email verification link hits
        RateLimiter::for('verification.verify', function (Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });

        // Resend verification email
        RateLimiter::for('verification.resend', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perHour(6)->by($request->ip()),
            ];
        });

        // UGC writes (comments/replies)
        RateLimiter::for('ugc-write', function (Request $request) {
            $by = optional($request->user())->id ?? $request->ip();
            return [
                Limit::perMinute(10)->by($by),
                Limit::perHour(120)->by($by),
            ];
        });

        // Contact form submit
        RateLimiter::for('contact.submit', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perHour(15)->by($request->ip()),
            ];
        });
    }
}