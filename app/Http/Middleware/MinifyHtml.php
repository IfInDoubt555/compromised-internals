<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinifyHtml
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Only HTML responses
        if (
            str_contains($response->headers->get('Content-Type', ''), 'text/html')
            && $response->getStatusCode() === 200
        ) {
            $output = $response->getContent();

            // 1) Remove HTML comments, except conditional IE ones
            $output = preg_replace(
                '/<!--(?!\[if).*?-->/s',
                '',
                $output
            );

            // 2) Collapse whitespace (but keep single spaces)
            $output = preg_replace(
                '/\s{2,}/',
                ' ',
                $output
            );

            // 3) Remove spaces between tags
            $output = preg_replace(
                '/>\s+</',
                '><',
                $output
            );

            $response->setContent($output);
        }

        return $response;
    }
}