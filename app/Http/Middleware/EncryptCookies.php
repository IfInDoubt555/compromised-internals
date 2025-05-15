<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    // Add any cookies that shouldn't be encrypted, if needed
    protected $except = [];
}
