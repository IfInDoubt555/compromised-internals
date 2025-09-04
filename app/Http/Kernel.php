<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Auth\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\GatekeeperMiddleware;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            SubstituteBindings::class,
            \App\Http\Middleware\RejectEmptyUserAgent::class,
            \App\Http\Middleware\MinifyHtml::class,
            \Spatie\Csp\AddCspHeaders::class,
        ],

        'api' => [
            ThrottleRequests::class . ':api',
            SubstituteBindings::class,
        ],
    ];

    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'guest' => RedirectIfAuthenticated::class,
        'verified' => EnsureEmailIsVerified::class,
    ];
}
