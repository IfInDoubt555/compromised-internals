<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PostModerationController;
use App\Http\Controllers\Admin\AdminRallyEventController;
use App\Http\Controllers\AttributionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\EmailController;

// Route group is already prefixed + named in RouteServiceProvider
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

Route::get('/attributions', [AttributionController::class, 'index'])->name('attributions.index');
Route::post('/attributions', [AttributionController::class, 'update'])->name('attributions.bulkUpdate');

Route::prefix('posts')->name('posts.')->group(function () {
    Route::get('/moderation', [PostModerationController::class, 'index'])->name('moderation');
    Route::post('/{post}/approve', [PostModerationController::class, 'approve'])->name('approve')->whereNumber('post');
    Route::post('/{post}/reject', [PostModerationController::class, 'reject'])->name('reject')->whereNumber('post');
});

Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [AdminRallyEventController::class, 'index'])->name('index');
    Route::get('/create', [AdminRallyEventController::class, 'create'])->name('create');
    Route::post('/', [AdminRallyEventController::class, 'store'])->name('store');
    Route::get('/{event}/edit', [AdminRallyEventController::class, 'edit'])->name('edit');
    Route::put('/{event}', [AdminRallyEventController::class, 'update'])->name('update');
    Route::delete('/{event}', [AdminRallyEventController::class, 'destroy'])->name('destroy');
});

Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [AdminUserController::class, 'index'])->name('index');
    Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');

    Route::post('/{user}/ban', [AdminUserController::class, 'ban'])->name('ban');
    Route::post('/{user}/unban', [AdminUserController::class, 'unban'])->name('unban');
});

Route::prefix('emails')->name('emails.')->middleware(['auth', 'can:access-admin'])->group(function () {
    Route::get('/', [EmailController::class, 'index'])->name('index');
    Route::get('/{id}', [EmailController::class, 'show'])->name('show');
    Route::patch('/{id}/resolve', [EmailController::class, 'toggleResolved'])->name('toggleResolved');
    Route::patch('/{id}/category', [EmailController::class, 'updateCategory'])->name('updateCategory');
});
