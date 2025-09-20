<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Console\Commands\PruneSessions;
use Spatie\Csp\AddCspHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // 👇 ensure our admin routes are always registered
        then: function () {
            \Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'can:access-admin'])
                ->prefix('admin')
                ->as('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withProviders([
        \App\Providers\AppServiceProvider::class,
        \App\Providers\AuthServiceProvider::class,
        // \App\Providers\EventServiceProvider::class,
        \App\Providers\RouteServiceProvider::class, // harmless to keep; the explicit loader above wins
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withMiddleware(function (Middleware $middleware) {
        $appEnv = $_ENV['APP_ENV'] ?? 'production';
        $cspOn  = (($_ENV['CSP_ENABLED'] ?? 'true') === 'true');
        $isProd = $appEnv === 'production';

        $middleware->group('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('api', [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth'     => \App\Http\Middleware\Authenticate::class,
            'guest'    => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'can'      => \Illuminate\Auth\Middleware\Authorize::class, // needed for can:access-admin
        ]);

        if ($isProd && $cspOn) {
            $middleware->append(AddCspHeaders::class);
        }
    })
    ->withCommands([
        PruneSessions::class,
        App\Console\Commands\PruneSessions::class,
        App\Console\Commands\ScanImageAttributions::class,
        App\Console\Commands\FixUnicodeEscapes::class, // ← add
    ])
    ->create();