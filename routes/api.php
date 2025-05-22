<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RallyEventController;
use App\Http\Controllers\StripeWebhookController;

Route::middleware('auth:web')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/events', [RallyEventController::class, 'api']);

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);