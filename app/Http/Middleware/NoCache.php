<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoCache
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
