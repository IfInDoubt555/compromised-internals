<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global middleware
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        // Add your global ones here if you use them:
        // \App\Http\Middleware\TrustProxies::class,
        // \App\Http\Middleware\CspHeaders::class, // if you want CSP global via Kernel instead of bootstrap
    ];

    /**
     * Middleware groups
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,     // your app's CSRF middleware
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // your custom web middlewares (if these classes exist)
            \App\Http\Middleware\RejectEmptyUserAgent::class,
            \App\Http\Middleware\MinifyHtml::class,
            \App\Http\Middleware\CspHeaders::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Route middleware aliases
     */
    protected $middlewareAliases = [
        'auth'              => \App\Http\Middleware\Authenticate::class,
        'guest'             => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'verified'          => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // common core aliases you’re using elsewhere:
        'can'               => \Illuminate\Auth\Middleware\Authorize::class,
        'signed'            => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'password.confirm'  => \Illuminate\Auth\Middleware\RequirePassword::class,
        'throttle'          => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'substitute.bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ];
}