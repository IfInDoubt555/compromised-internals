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
use App\Models\RallyEvent;
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
use App\Http\Controllers\TravelPageController;
use App\Http\Controllers\AffiliateRedirectController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\Admin\PostModerationController;
use App\Http\Controllers\CalendarLegacyRedirectController;
use App\Http\Controllers\PublicProfileController;

Route::get('/', [HomeController::class, 'index'])->name('home');

/* ------------------ Affiliate ------------*/
Route::get('/out', AffiliateRedirectController::class)
    ->middleware('throttle:300,1') // 300 requests per minute per IP
    ->name('out');

/* ----------------- Boards (public) ----------------- */
Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
Route::get('/boards/{board:slug}', [BoardController::class, 'show'])->name('boards.show');

/* ----------------- Threads ----------------- */
// public show
Route::get('/threads/{thread:slug}', [ThreadController::class, 'show'])->name('threads.show');

// create/store/edit/delete (auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/boards/{board:slug}/threads/create', [ThreadController::class, 'create'])->name('threads.create');
    Route::post('/boards/{board:slug}/threads',        [ThreadController::class, 'store'])->name('threads.store');

    Route::get   ('/threads/{thread:slug}/edit', [ThreadController::class, 'edit'])->name('threads.edit');
    Route::patch ('/threads/{thread:slug}',      [ThreadController::class, 'update'])->name('threads.update');
    Route::delete('/threads/{thread:slug}',      [ThreadController::class, 'destroy'])->name('threads.destroy');

    Route::post  ('/threads/{thread:slug}/replies', [ReplyController::class, 'store'])
        ->middleware('throttle:ugc-write')->name('replies.store');
    Route::patch ('/replies/{reply}',               [ReplyController::class, 'update'])
        ->middleware('throttle:ugc-write')->name('replies.update');
    Route::delete('/replies/{reply}',               [ReplyController::class, 'destroy'])
        ->middleware('throttle:ugc-write')->name('replies.destroy');
});

/* ----------------- Calendar ----------------- */
// Calendar home
Route::get('/calendar', [RallyEventController::class, 'index'])->name('calendar.index');

// JSON API used by the calendar UI
Route::get('/calendar/events', [RallyEventController::class, 'api'])->name('calendar.api');

// Legacy numeric links: /calendar/events/{id}  ->  /calendar/{slug}
Route::get('/calendar/events/{event:id}', [CalendarLegacyRedirectController::class, 'byId'])
    ->whereNumber('event')->name('calendar.events.legacy');

// Legacy slug links that used the /calendar/events/ prefix (avoid numbers-only)
Route::get('/calendar/events/{slug}', [CalendarLegacyRedirectController::class, 'bySlug'])
    ->where('slug', '^(?!\d+$)[a-z0-9-]+$')->name('calendar.events.legacy.slug');

// Canonical event page (slug-bound model)
Route::get('/calendar/{rallyEvent:slug}', [RallyEventController::class, 'show'])->name('calendar.show');

// iCal endpoints
Route::get('/calendar/feed/{year}.ics', [CalendarExportController::class, 'year'])
    ->whereNumber('year')->name('calendar.feed.year');

Route::get('/calendar/download/{year}.ics', [CalendarExportController::class, 'download'])
    ->whereNumber('year')->name('calendar.download.year');

/* ----------------- Cart ----------------- */
Route::prefix('shop/cart')->name('shop.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::post('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::get('/remove/{id}', [CartController::class, 'remove'])->name('remove');
});

Route::view('/rally-resources', 'rally-resources.resources')->name('resources');

Route::get('/cart/count', function () {
    return response()->json(['count' => session('cart') ? count(session('cart')) : 0]);
})->name('cart.count');

/* ----------------- Shop ----------------- */
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('shop.show');

/* ----------------- Checkout ----------------- */
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
Route::view('/checkout/unavailable', 'errors.checkout-unavailable')->name('errors.checkout-unavailable');

/* ----------------- Charity ----------------- */
Route::get('/charity', [CharityController::class, 'index'])->name('charity.index');

/* ----------------- Dashboard ----------------- */
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

/* ----------------- Blog ----------------- */
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/{post}', [PostController::class, 'show'])->name('show');

    Route::middleware(['auth'])->group(function () {
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::post('/', [PostController::class, 'store'])->name('store');
    });
});

/* ----------------- History ----------------- */
Route::prefix('history')->name('history.')->group(function () {
    Route::get('/', [HistoryController::class, 'index'])->name('index');

    // e.g. /history/events/2000/787  or  /history/events/2000s/787  or slug ids
    Route::get('/{tab}/{decade}/{id}', [HistoryController::class, 'show'])
        ->where([
            'tab'    => '(events|cars|drivers)',
            'decade' => '\d{4}s?',      // 4 digits with optional trailing "s"
            'id'     => '[-a-z0-9]+'    // numeric id or kebab-case slug
        ])
        ->name('show');
});

/* ----------------- Travel ----------------- */
// Landing page used by the Home hero link
Route::get('/travel/plan', [TravelPageController::class, 'index'])->name('travel.plan');

// Individual travel page for a rally event (slug binding)
Route::get('/travel/event/{rallyEvent:slug}', [TravelPageController::class, 'event'])->name('travel.plan.event');

/* ----------------- Auth-required ----------------- */
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
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
        ->middleware('throttle:ugc-write')->name('comments.store');
    Route::post('/comments/{comment}/edit', [CommentController::class, 'update'])
        ->middleware('throttle:ugc-write')->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->middleware('throttle:ugc-write')->name('comments.destroy');
});

// Public post show
Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');

/* ----------------- Public profile ----------------- */
Route::get('/profile/{user}', [PublicProfileController::class, 'show'])
    ->middleware(['auth', 'verified'])->name('profile.public');

/* ----------------- Contact ----------------- */
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])
    ->middleware('throttle:contact.submit')->name('contact.submit');

/* ----------------- Footer pages ----------------- */
Route::view('/terms', 'footer.terms')->name('terms');
Route::view('/privacy', 'footer.privacy')->name('privacy');

/* ----------------- Sitemap ----------------- */
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemaps/static.xml', [SitemapController::class, 'static'])->name('sitemap.static');
Route::get('/sitemaps/blog.xml', [SitemapController::class, 'blog'])->name('sitemap.blog');
Route::get('/sitemaps/calendar.xml', [SitemapController::class, 'calendar'])->name('sitemap.calendar');
Route::get('/sitemaps/history.xml', [SitemapController::class, 'history'])->name('sitemap.history');

/* ----------------- Security ----------------- */
Route::view('/security/policy', 'security.policy')->name('security.policy');
Route::view('/security/hall-of-fame', 'security.hall-of-fame')->name('security.hof');

/* ----------------- Publish Routes ----------------- */
Route::middleware(['auth' /* , 'verified' , 'can:admin' */])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        // Publisher (queue + create + preview)
        Route::get('/publish',                    [PublisherController::class, 'index'])->name('publish.index');
        Route::get('/publish/create',             [PublisherController::class, 'create'])->name('publish.create');
        Route::post('/publish',                   [PublisherController::class, 'store'])->name('publish.store');
        Route::get('/publish/preview/{post:slug}',[PublisherController::class, 'preview'])->name('publish.preview');

        // Quick actions used by the preview page
        Route::post('/publish/{post:slug}/publish-now', [PublisherController::class, 'publishNow'])
            ->name('publish.publishNow');
        Route::post('/publish/{post:slug}/schedule',    [PublisherController::class, 'schedule'])
            ->name('publish.schedule');

        // Moderation (edit/update existing posts in admin)
        Route::get('/posts/moderation', [PostModerationController::class, 'index'])->name('posts.moderation');
        Route::get('/posts/{post}/edit', [PostModerationController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}',      [PostModerationController::class, 'update'])->name('posts.update');
    });

require __DIR__ . '/auth.php';

/* ----------------- Optional: Event details alias ----------------- */
/* Keep both URLs working: /calendar/{slug} and /events/{slug} */
Route::get('/events/{rallyEvent:slug}', [RallyEventController::class, 'show'])->name('events.show');