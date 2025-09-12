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
        if (!app()->isLocal()) {
        // Do NOT redefine 'web'/'api' groups and aliases here since Kernel owns them.
        // If you want CSP globally, append it here (or keep it in Kernel/global).
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