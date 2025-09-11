<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,   // <-- needed for your policies & Gate('access-admin')
    App\Providers\RouteServiceProvider::class,
];