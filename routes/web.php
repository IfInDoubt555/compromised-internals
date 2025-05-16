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
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

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
Route::view('/checkout/unavailable', 'errors.checkout-unavailable')->name('errors.checkout-unavailable');

Route::get('/charity', [CharityController::class, 'index'])->name('charity.index');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/{post}', [PostController::class, 'show'])->name('show');

    Route::middleware(['auth'])->group(function () {
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::post('/', [PostController::class, 'store'])->name('store');
    });
});

Route::prefix('history')->name('history.')->group(function () {
    Route::get('/', [HistoryController::class, 'index'])->name('index');
    Route::get('/{tab}/{decade}/{id}', [HistoryController::class, 'show'])->name('show');
});

Route::middleware(['auth'])->group(function () {
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

    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/{comment}/edit', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');

Route::get('/profile/{user}', function (User $user) {
    return view('profile.public', compact('user'));
})->name('profile.public');


Route::get('/contact', [ContactController::class, 'show'])
    ->name('contact');

Route::post('/contact', [ContactController::class, 'submit'])
    ->name('contact.submit');

Route::view('/terms', 'footer.terms')->name('terms');
Route::view('/privacy', 'footer.privacy')->name('privacy');

Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create()
        ->add(Url::create('/'))
        ->add(Url::create('/blog'))
        ->add(Url::create('/shop'))
        ->add(Url::create('/history'))
        ->add(Url::create('/events'));

    // Optionally: loop through posts/events/products
    foreach (\App\Models\Post::all() as $post) {
        $sitemap->add(Url::create(route('blog.show', $post->slug)));
    }

    foreach (\App\Models\RallyEvent::all() as $event) {
        $sitemap->add(Url::create(route('events.show', $event->slug)));
    }

    return $sitemap->toResponse(request());
});

Route::view('/security/policy', 'security.policy')->name('security.policy');

require __DIR__ . '/auth.php';