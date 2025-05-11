<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttributionController;
use App\Http\Controllers\Admin\PostModerationController;
use App\Models\User;

Route::get('/', function () {
    $userCount = User::count();
    return view('admin.dashboard', compact('userCount'));
})->name('dashboard');


// Image attributions
Route::get('/attributions', [AttributionController::class, 'index'])->name('attributions.index');
Route::post('/attributions', [AttributionController::class, 'update'])->name('attributions.bulkUpdate');

// Post moderation routes
Route::prefix('posts')->name('posts.')->group(function () {
    Route::get('/moderation', [PostModerationController::class, 'index'])->name('moderation');
    Route::post('/{post}/approve', [PostModerationController::class, 'approve'])
        ->name('approve')
        ->whereNumber('post');

    Route::post('/{post}/reject', [PostModerationController::class, 'reject'])
        ->name('reject')
        ->whereNumber('post');
});
