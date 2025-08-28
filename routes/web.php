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
use App\Models\RallyEvent; // ← added for legacy redirect binding
use App\Http\Controllers\AttributionController;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\CalendarExportController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Boards (public)
Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
Route::get('/boards/{board:slug}', [BoardController::class, 'show'])->name('boards.show');

// Threads (public show)
Route::get('/threads/{thread:slug}', [ThreadController::class, 'show'])->name('threads.show');

// Threads (create/store under a specific board — auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/boards/{board:slug}/threads/create', [ThreadController::class, 'create'])->name('threads.create');
    Route::post('/boards/{board:slug}/threads',        [ThreadController::class, 'store'])->name('threads.store');

    Route::get   ('/threads/{thread:slug}/edit', [ThreadController::class, 'edit'])->name('threads.edit');
    Route::patch ('/threads/{thread:slug}',      [ThreadController::class, 'update'])->name('threads.update');
    Route::delete('/threads/{thread:slug}',      [ThreadController::class, 'destroy'])->name('threads.destroy');

    Route::post  ('/threads/{thread:slug}/replies', [ReplyController::class, 'store'])->name('replies.store');
    Route::patch ('/replies/{reply}',               [ReplyController::class, 'update'])->name('replies.update');
    Route::delete('/replies/{reply}',               [ReplyController::class, 'destroy'])->name('replies.destroy');
});

// Calendar
Route::get('/calendar', [RallyEventController::class, 'index'])->name('calendar');
Route::get('/calendar/events', [RallyEventController::class, 'api'])->name('calendar.api');

// Legacy: redirect old /calendar/events/{id} links to canonical slug route
Route::get('/calendar/events/{event}', function (RallyEvent $event) {
    return redirect()->route('calendar.show', $event->slug, 301);
})->whereNumber('event')->name('calendar.events.legacy');

Route::get('/calendar/{slug}', [RallyEventController::class, 'show'])->name('calendar.show');

Route::get('/calendar/feed/{year}.ics', [CalendarExportController::class, 'year'])
     ->whereNumber('year')
     ->name('calendar.feed.year');

Route::get('/calendar/download/{year}.ics', [CalendarExportController::class, 'download'])
     ->whereNumber('year')
     ->name('calendar.download.year');

// Cart
Route::prefix('shop/cart')->name('shop.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::get('/remove/{id}', [CartController::class, 'remove'])->name('remove');
});

Route::view('/rally-resources', 'rally-resources.resources')->name('resources');

Route::get('/cart/count', function () {
    return response()->json([
        'count' => session('cart') ? count(session('cart')) : 0
    ]);
})->name('cart.count');

// Shop
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('shop.show');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
Route::view('/checkout/unavailable', 'errors.checkout-unavailable')->name('errors.checkout-unavailable');

// Charity
Route::get('/charity', [CharityController::class, 'index'])->name('charity.index');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Blog
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/{post}', [PostController::class, 'show'])->name('show');

    Route::middleware(['auth'])->group(function () {
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::post('/', [PostController::class, 'store'])->name('store');
    });
});

// History
Route::prefix('history')->name('history.')->group(function () {
    Route::get('/', [HistoryController::class, 'index'])->name('index');
    Route::get('/{tab}/{decade}/{id}', [HistoryController::class, 'show'])->name('show');
});

// Travel
Route::view('/travel', 'travel.index')->name('travel.index');

// Auth-required routes
Route::middleware(['auth', 'verified'])->group(function () {
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

// Public profile
Route::get('/profile/{user}', function (User $user) {
    return view('profile.public', compact('user'));
})->middleware(['auth', 'verified'])->name('profile.public');

// Contact
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Footer pages
Route::view('/terms', 'footer.terms')->name('terms');
Route::view('/privacy', 'footer.privacy')->name('privacy');

// Sitemap
Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create()
        ->add(Url::create('/'))
        ->add(Url::create('/blog'))
        ->add(Url::create('/shop'))
        ->add(Url::create('/history'))
        ->add(Url::create('/calendar'));

    foreach (\App\Models\Post::all() as $post) {
        if ($post->slug) {
            $sitemap->add(Url::create(route('blog.show', $post->slug)));
        }
    }

    foreach (RallyEvent::all() as $event) {
        if ($event->slug) {
            $sitemap->add(Url::create(route('calendar.show', $event->slug)));
        }
    }

    return response($sitemap->render(), 200, ['Content-Type' => 'application/xml']);
});

// Security
Route::view('/security/policy', 'security.policy')->name('security.policy');
Route::view('/security/hall-of-fame', 'security.hall-of-fame')->name('security.hof');

require __DIR__ . '/auth.php';