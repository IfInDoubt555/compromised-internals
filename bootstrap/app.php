<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Console\Commands\PruneSessions;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use App\Http\Kernel as AppHttpKernel;
use Spatie\Csp\AddCspHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withMiddleware(function (Middleware $middleware) {
        // Only send CSP headers outside local dev
        if (app()->environment('production')) {
            $middleware->append(AddCspHeaders::class);
        }
    })
    ->withCommands([
        PruneSessions::class,
        \App\Console\Commands\ScanImageAttributions::class,
    ])
    ->withBindings([
        // 🔧 This was wrong before. Bind the *interface* to your App Kernel.
        HttpKernel::class => AppHttpKernel::class,
    ])
    ->create();