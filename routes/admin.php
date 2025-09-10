<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PostModerationController;
use App\Http\Controllers\Admin\AdminRallyEventController;
use App\Http\Controllers\AttributionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\EventDayController;
use App\Http\Controllers\Admin\StageController;
use App\Http\Controllers\Admin\TravelHighlightController;
use App\Http\Controllers\Admin\AffiliateClickController;
use App\Http\Controllers\Admin\ThreadAdminController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\Admin\AdminScheduledController;

// NOTE: RouteServiceProvider already wraps this file with:
// prefix('admin')->name('admin.') and web+auth+access-admin middleware

/* ---------- Dashboard ---------- */
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

/* ---------- Image Attributions ---------- */
Route::get('/attributions', [AttributionController::class, 'index'])->name('attributions.index');
Route::post('/attributions', [AttributionController::class, 'update'])->name('attributions.bulkUpdate');

/* ---------- Affiliate Clicks ---------- */
Route::prefix('affiliates')->name('affiliates.')->group(function () {
    Route::get('/clicks', [AffiliateClickController::class, 'index'])->name('clicks');
    Route::get('/clicks/export', [AffiliateClickController::class, 'export'])->name('clicks.export');
    Route::get('/clicks/chart-data', [AffiliateClickController::class, 'chartData'])->name('clicks.chart');
});

/* ---------- Publisher: Queue + Compose + Preview ---------- */
// Queue at /admin/publish
Route::get('/publish', [PublisherController::class, 'index'])->name('publish.index');
// Compose at /admin/publish/create
Route::get('/publish/create', [PublisherController::class, 'create'])->name('publish.create');
Route::post('/publish', [PublisherController::class, 'store'])->name('publish.store');

// Admin-only preview (draft/scheduled)
Route::get('/publish/preview/{post}', [PublisherController::class, 'preview'])->name('publish.preview');
// Quick actions
Route::post('/publish/{post}/publish-now', [PublisherController::class, 'publishNow'])->name('publish.now');
Route::post('/publish/{post}/schedule', [PublisherController::class, 'schedule'])->name('publish.schedule');

/* ---------- Rally Events CRUD ---------- */
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/',                [AdminRallyEventController::class, 'index'])->name('index');
    Route::get('/create',          [AdminRallyEventController::class, 'create'])->name('create');
    Route::post('/',               [AdminRallyEventController::class, 'store'])->name('store');
    Route::get('/{event}/edit',    [AdminRallyEventController::class, 'edit'])->name('edit');
    Route::put('/{event}',         [AdminRallyEventController::class, 'update'])->name('update');
    Route::delete('/{event}',      [AdminRallyEventController::class, 'destroy'])->name('destroy');

    // Days
    Route::get('/{event}/days',            [EventDayController::class, 'index'])->name('days.index');
    Route::post('/{event}/days',           [EventDayController::class, 'store'])->name('days.store');
    Route::delete('/{event}/days/{day}',   [EventDayController::class, 'destroy'])->name('days.destroy');

    // Stages
    Route::get('/{event}/stages',                  [StageController::class, 'index'])->name('stages.index');
    Route::post('/{event}/stages',                 [StageController::class, 'store'])->name('stages.store');
    Route::get('/{event}/stages/{stage}/edit',     [StageController::class, 'edit'])->name('stages.edit');
    Route::put('/{event}/stages/{stage}',          [StageController::class, 'update'])->name('stages.update');
    Route::delete('/{event}/stages/{stage}',       [StageController::class, 'destroy'])->name('stages.destroy');
});

/* ---------- Posts Moderation ---------- */
Route::prefix('posts')->name('posts.')->group(function () {
    Route::get('/moderation',  [PostModerationController::class, 'index'])->name('moderation');
    // Do NOT force numeric IDs; Post binds by slug. If you want ID, use {post:id}.
    Route::post('/{post}/approve', [PostModerationController::class, 'approve'])->name('approve');
    Route::post('/{post}/reject',  [PostModerationController::class, 'reject'])->name('reject');

    Route::get('/{post}/edit', [PostModerationController::class, 'edit'])->name('edit');
    Route::put('/{post}',      [PostModerationController::class, 'update'])->name('update');
});

/* ---------- Threads Admin ---------- */
Route::prefix('threads')->name('threads.')->group(function () {
    Route::get('/',              [ThreadAdminController::class, 'index'])->name('index');
    Route::get('/{thread}/edit', [ThreadAdminController::class, 'edit'])->name('edit');
    Route::put('/{thread}',      [ThreadAdminController::class, 'update'])->name('update');
});

/* ---------- Optional: Unified Scheduled Overview ---------- */
Route::get('/scheduled', [AdminScheduledController::class, 'index'])->name('scheduled');

/* ---------- Travel Highlights ---------- */
Route::resource('travel-highlights', TravelHighlightController::class)
    ->except(['show'])
    ->names('travel-highlights');

Route::get('travel-highlights/tips', [TravelHighlightController::class, 'editTips'])
    ->name('travel-highlights.tips.edit');
Route::put('travel-highlights/tips', [TravelHighlightController::class, 'updateTips'])
    ->name('travel-highlights.tips.update');

/* ---------- Users ---------- */
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/',              [AdminUserController::class, 'index'])->name('index');
    Route::get('/{user}',        [AdminUserController::class, 'show'])->name('show');
    Route::post('/{user}/ban',   [AdminUserController::class, 'ban'])->name('ban');
    Route::post('/{user}/unban', [AdminUserController::class, 'unban'])->name('unban');
});

/* ---------- Emails ---------- */
Route::prefix('emails')->name('emails.')->group(function () {
    Route::get('/',                 [EmailController::class, 'index'])->name('index');
    Route::get('/{id}',             [EmailController::class, 'show'])->name('show');
    Route::patch('/{id}/resolve',   [EmailController::class, 'toggleResolved'])->name('toggleResolved');
    Route::patch('/{id}/category',  [EmailController::class, 'updateCategory'])->name('updateCategory');
    Route::patch('/{message}/archive', [EmailController::class, 'archive'])->name('archive');
});