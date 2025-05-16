<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Console\Commands\PruneSessions;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use App\Http\Kernel as AppHttpKernel;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withExceptions(function ($exceptions) {
        //
    })
    ->withMiddleware(function (Middleware $middleware) {
        // Register global middleware, middleware groups, and aliases
        // Laravel 12 automatically uses your App\Http\Kernel::class
    })
    ->withCommands([
        \App\Console\Commands\PruneSessions::class,
        \App\Console\Commands\ScanImageAttributions::class,
    ])
    ->withBindings([
        \Illuminate\Contracts\Http\Kernel::class => \App\Http\Kernel::class,
    ])
    ->create();