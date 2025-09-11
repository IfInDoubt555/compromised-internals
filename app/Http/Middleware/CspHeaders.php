<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CspHeaders
{
    public function handle(Request $request, Closure $next)
    {
        // Generate a per-request nonce and share to Blade
        $nonce = base64_encode(random_bytes(16));
        view()->share('cspNonce', $nonce); // use as: nonce="@cspNonce"

        $response = $next($request);

        // Strict CSP with nonce (no unsafe-eval needed because we use @alpinejs/csp)
        $policy = "default-src 'self'; "
                . "script-src 'self' 'nonce-{$nonce}' https://cmp.osano.com; "
                . "style-src 'self' 'unsafe-inline'; "
                . "style-src-attr 'self' 'unsafe-inline'; "
                . "img-src 'self' data: blob: https:; "
                . "font-src 'self' data:; "
                . "connect-src 'self' https://cmp.osano.com; "
                . "frame-src https://cmp.osano.com; "
                . "base-uri 'self'; "
                . "form-action 'self'; "
                . "object-src 'none';";

        $response->headers->set('Content-Security-Policy', $policy);

        return $response;
    }
}