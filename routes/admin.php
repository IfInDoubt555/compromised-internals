<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttributionController;

Route::get('/attributions', [AttributionController::class, 'index'])->name('attributions.index');
Route::post('/attributions', [AttributionController::class, 'update'])->name('attributions.update');

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');