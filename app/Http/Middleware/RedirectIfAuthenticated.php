<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * @param  string  ...$guards
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response|RedirectResponse
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        /** @var Response $resp */
        $resp = $next($request);
        return $resp;
    }
}