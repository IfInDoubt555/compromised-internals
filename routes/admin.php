<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttributionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PostModerationController;
use App\Http\Controllers\Admin\AdminRallyEventController;


// Admin Dashboard
Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

// Image Attributions
Route::get('/attributions', [AttributionController::class, 'index'])->name('attributions.index');
Route::post('/attributions', [AttributionController::class, 'update'])->name('attributions.bulkUpdate');

// Blog Post Moderation
Route::prefix('posts')->name('posts.')->group(function () {
    Route::get('/moderation', [PostModerationController::class, 'index'])->name('moderation');

    Route::post('/{post}/approve', [PostModerationController::class, 'approve'])
        ->name('approve')
        ->whereNumber('post');

    Route::post('/{post}/reject', [PostModerationController::class, 'reject'])
        ->name('reject')
        ->whereNumber('post');
});

Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [AdminRallyEventController::class, 'index'])->name('index');
    Route::get('/create', [AdminRallyEventController::class, 'create'])->name('create');
    Route::post('/', [AdminRallyEventController::class, 'store'])->name('store');
});
