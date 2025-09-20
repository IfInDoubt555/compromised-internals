<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Console\Commands\PruneSessions;
use App\Http\Kernel as AppHttpKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Spatie\Csp\AddCspHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
        ->withProviders([
        \App\Providers\AppServiceProvider::class,
        \App\Providers\AuthServiceProvider::class,
  //    \App\Providers\EventServiceProvider::class,
        \App\Providers\RouteServiceProvider::class, // 👈 loads routes/admin.php
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withMiddleware(function (Middleware $middleware) {
        // Use $_ENV directly; do NOT call env()/app() here.
        $appEnv   = $_ENV['APP_ENV']       ?? 'production';
        $cspOn    = ($_ENV['CSP_ENABLED']  ?? 'true') === 'true';
        $isProd   = $appEnv === 'production';

        // web/api groups and aliases (leave as you already had them)
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
        ]);

        // Append CSP only in production (prevents local CSP breakage)
        if ($isProd && $cspOn) {
            $middleware->append(AddCspHeaders::class);
        }
    })
    ->withCommands([
        PruneSessions::class,
        \App\Console\Commands\ScanImageAttributions::class,
    ])
    ->withBindings([
        HttpKernel::class => AppHttpKernel::class,
    ])
    ->create();