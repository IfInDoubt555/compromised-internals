<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RejectEmptyUserAgent
{
    public function handle(Request $request, Closure $next): Response
    {
        $ua = $request->header('User-Agent', '');

        // 1) Block empty UAs
        if (trim($ua) === '') {
            abort(Response::HTTP_FORBIDDEN, 'No UA? No entry.');
        }

        // 2) Block that specific offending IP
        if ($request->ip() === '103.177.150.28') {
            abort(Response::HTTP_FORBIDDEN, 'Your IP is not welcome here.');
        }

        // 3) (Optional) Block the entire 196.251.0.0/16 subnet
        if (str_starts_with((string) $request->ip(), '196.251.')) {
            abort(Response::HTTP_FORBIDDEN, 'Your IP range is not welcome here.');
        }

        /** @var Response $resp */
        $resp = $next($request);
        return $resp;
    }
}