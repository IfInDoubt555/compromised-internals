<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttributionController;
use App\Http\Controllers\Admin\PostModerationController;


Route::get('/attributions', [AttributionController::class, 'index'])->name('attributions.index');
Route::post('/attributions', [AttributionController::class, 'update'])->name('attributions.bulkUpdate');

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');

Route::prefix('posts')->name('posts.')->group(function () {
    Route::get('/moderation', [PostModerationController::class, 'index'])->name('moderation');
    Route::post('/{post}/approve', [PostModerationController::class, 'approve'])->name('approve');
    Route::post('/{post}/reject', [PostModerationController::class, 'reject'])->name('reject');
});