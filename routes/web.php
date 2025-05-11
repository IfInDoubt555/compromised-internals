<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RallyEventController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CharityController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Models\User;
use App\Http\Controllers\AttributionController;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/calendar', [RallyEventController::class, 'index'])->name('calendar');
Route::get('/calendar/events', [RallyEventController::class, 'api'])->name('calendar.api');
Route::get('/calendar/{slug}', [RallyEventController::class, 'show'])->name('calendar.show');

Route::prefix('shop/cart')->name('shop.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::get('/remove/{id}', [CartController::class, 'remove'])->name('remove');
});

Route::get('/cart/count', function () {
    return response()->json([
        'count' => session('cart') ? count(session('cart')) : 0
    ]);
})->name('cart.count');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('shop.show');


Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
Route::view('/checkout/unavailable', 'errors.checkout-unavailable')
    ->name('errors.checkout-unavailable');


Route::get('/charity', [CharityController::class, 'index'])->name('charity.index');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/{post}', [PostController::class, 'show'])->name('show');

    Route::middleware('auth')->group(function () {
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::post('/', [PostController::class, 'store'])->name('store');
    });
});

Route::prefix('history')->name('history.')->group(function () {
    Route::get('/', [HistoryController::class, 'index'])->name('index');
    Route::get('/{tab}/{decade}/{id}', [HistoryController::class, 'show'])->name('show');
});


Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/orders', [DashboardController::class, 'orders'])->name('profile.orders');
    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit'); 
    Route::patch('posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});
Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/profile/{user}', function (User $user) {
    return view('profile.public', compact('user'));
})->name('profile.public');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Route::get('/check-admin-gate', function () {
//     return Gate::allows('access-admin') ? '✅ Gate allows access' : '❌ Gate denies access';
// })->middleware('auth');

require __DIR__.'/auth.php';