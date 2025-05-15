<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // Handle 403 Forbidden
        if ($exception instanceof AuthorizationException) {
            return response()->view('errors.403', [], 403);
        }

        // Handle HTTP exceptions with matching error views
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();

            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", [], $statusCode);
            }
        }

        return parent::render($request, $exception);
    }
}