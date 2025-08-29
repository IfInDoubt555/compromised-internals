<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PostModerationController;
use App\Http\Controllers\Admin\AdminRallyEventController;
use App\Http\Controllers\AttributionController;
use App\Http\Controllers\Admin\UserManagementController; // if used
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\EventDayController;
use App\Http\Controllers\Admin\StageController;
use App\Http\Controllers\Admin\TravelHighlightController;
use App\Http\Controllers\Admin\AffiliateClickController;

/**
 * NOTE: This file is already wrapped in RouteServiceProvider with:
 * prefix('admin')->name('admin.')
 */

Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

Route::get('/attributions', [AttributionController::class, 'index'])->name('attributions.index');
Route::post('/attributions', [AttributionController::class, 'update'])->name('attributions.bulkUpdate');

/* ---------- Affiliate Clicks ---------- */
Route::get('/affiliates/clicks', [AffiliateClickController::class, 'index'])
    ->name('affiliates.clicks');

Route::get('/affiliates/clicks/export', [AffiliateClickController::class, 'export'])
    ->name('affiliates.clicks.export');

/* ---------- Admin Rally Events CRUD ---------- */
Route::prefix('events')->name('events.')->group(function () {
    // Main CRUD (admin.events.*)
    Route::get('/',                [AdminRallyEventController::class, 'index'])->name('index');
    Route::get('/create',          [AdminRallyEventController::class, 'create'])->name('create');
    Route::post('/',               [AdminRallyEventController::class, 'store'])->name('store');
    Route::get('/{event}/edit',    [AdminRallyEventController::class, 'edit'])->name('edit');
    Route::put('/{event}',         [AdminRallyEventController::class, 'update'])->name('update');
    Route::delete('/{event}',      [AdminRallyEventController::class, 'destroy'])->name('destroy');

    // Days (admin.events.days.*)
    Route::get('/{event}/days',            [EventDayController::class, 'index'])->name('days.index');
    Route::post('/{event}/days',           [EventDayController::class, 'store'])->name('days.store');
    Route::delete('/{event}/days/{day}',   [EventDayController::class, 'destroy'])->name('days.destroy');

    // Stages (admin.events.stages.*)
    Route::get('/{event}/stages',                  [StageController::class, 'index'])->name('stages.index');
    Route::post('/{event}/stages',                 [StageController::class, 'store'])->name('stages.store');
    Route::get('/{event}/stages/{stage}/edit',     [StageController::class, 'edit'])->name('stages.edit');
    Route::put('/{event}/stages/{stage}',          [StageController::class, 'update'])->name('stages.update');
    Route::delete('/{event}/stages/{stage}',       [StageController::class, 'destroy'])->name('stages.destroy');
});

/* ---------- Posts Moderation ---------- */
Route::prefix('posts')->name('posts.')->group(function () {
    Route::get('/moderation',                [PostModerationController::class, 'index'])->name('moderation');
    Route::post('/{post}/approve',           [PostModerationController::class, 'approve'])->name('approve')->whereNumber('post');
    Route::post('/{post}/reject',            [PostModerationController::class, 'reject'])->name('reject')->whereNumber('post');
});

/* ---------- Users ---------- */
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/',              [AdminUserController::class, 'index'])->name('index');
    Route::get('/{user}',        [AdminUserController::class, 'show'])->name('show');
    Route::post('/{user}/ban',   [AdminUserController::class, 'ban'])->name('ban');
    Route::post('/{user}/unban', [AdminUserController::class, 'unban'])->name('unban');
});

/* ---------- Email Inbox ---------- */
Route::prefix('emails')->name('emails.')->group(function () {
    Route::get('/',                 [EmailController::class, 'index'])->name('index');
    Route::get('/{id}',             [EmailController::class, 'show'])->name('show');
    Route::patch('/{id}/resolve',   [EmailController::class, 'toggleResolved'])->name('toggleResolved');
    Route::patch('/{id}/category',  [EmailController::class, 'updateCategory'])->name('updateCategory');
    Route::patch('/{message}/archive', [EmailController::class, 'archive'])->name('archive');
});

/* ---------- Travel Highlights (Plan Your Trip) ---------- */
Route::resource('travel-highlights', TravelHighlightController::class)
    ->except(['show'])
    ->names('travel-highlights'); // => admin.travel-highlights.*

/* Travel Tips (singleton, lives in the same controller) */
Route::get('travel-highlights/tips', [TravelHighlightController::class, 'editTips'])
    ->name('travel-highlights.tips.edit');   // => admin.travel-highlights.tips.edit

Route::put('travel-highlights/tips', [TravelHighlightController::class, 'updateTips'])
    ->name('travel-highlights.tips.update'); // => admin.travel-highlights.tips.update