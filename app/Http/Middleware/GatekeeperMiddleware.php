<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GatekeeperMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->get('site_unlocked', false)) {
            return redirect()->route('gatekeeper.form');
        }

        return $next($request);
    }
}
