<?php

use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\GuestbookController as AdminGuestbookController;
use App\Http\Controllers\Admin\StoreCheckController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GuestbookController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ShopifyCheckController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SkillGroupController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SearchController;
use App\Support\Locale;
use Illuminate\Support\Facades\Route;

/*
| Public pages, registered once per language: English at "/", German at "/de".
| Use lroute('blog.index') in views to link to the current language.
*/
foreach (Locale::PREFIXES as $locale => $prefix) {
    Route::prefix($prefix)
        ->name($locale === Locale::default() ? '' : "{$locale}.")
        ->middleware("locale:{$locale}")
        ->group(function () {
            Route::get('/', HomeController::class)->name('home');
            Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
            Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
            Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
            Route::get('/search.json', SearchController::class)->name('search');
            Route::get('/newsletter/confirmed', [NewsletterController::class, 'confirmed'])->name('newsletter.confirmed');
            Route::get('/newsletter/unsubscribed', [NewsletterController::class, 'unsubscribed'])->name('newsletter.unsubscribed');

            Route::get('/tools', [ToolsController::class, 'index'])->name('tools.index');
            Route::get('/tools/shopify-hmac', [ToolsController::class, 'hmac'])->name('tools.hmac');
            Route::get('/tools/cron', [ToolsController::class, 'cron'])->name('tools.cron');
            Route::get('/tools/shopify-store-check', ShopifyCheckController::class)->name('tools.shopify-check');

            Route::get('/guestbook', [GuestbookController::class, 'index'])->name('guestbook.index');

            Route::get('/book', [BookingController::class, 'index'])->name('book.index');
            Route::get('/book/slots.json', [BookingController::class, 'slots'])->name('book.slots');
            Route::get('/book/requested', [BookingController::class, 'requested'])->name('book.requested');
            Route::get('/book/cancel/{token}', [BookingController::class, 'cancelForm'])->name('book.cancel');
        });
}

Route::post('/blog/{slug}/react', [ReactionController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('blog.react');

Route::post('/guestbook', [GuestbookController::class, 'store'])
    ->middleware('throttle:3,10')
    ->name('guestbook.store');

Route::post('/book', [BookingController::class, 'store'])
    ->middleware('throttle:3,10')
    ->name('book.store');
Route::post('/book/cancel/{token}', [BookingController::class, 'cancel'])->name('book.cancel.confirm');

Route::get('/feed', [FeedController::class, 'rss'])->name('feed');
Route::get('/sitemap.xml', [FeedController::class, 'sitemap'])->name('sitemap');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('newsletter.store');
Route::get('/newsletter/confirm/{subscriber}', [NewsletterController::class, 'confirm'])
    ->middleware('signed')
    ->name('newsletter.confirm');
Route::match(['get', 'post'], '/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
    ->name('newsletter.unsubscribe');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin.email'])
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('posts', PostController::class);

        Route::resource('experiences', ExperienceController::class)->except('show');
        Route::patch('experiences/{experience}/move/{direction}', [ExperienceController::class, 'move'])
            ->whereIn('direction', ['up', 'down'])->name('experiences.move');

        Route::resource('projects', AdminProjectController::class)->except('show');
        Route::patch('projects/{project}/move/{direction}', [AdminProjectController::class, 'move'])
            ->whereIn('direction', ['up', 'down'])->name('projects.move');

        Route::resource('skills', SkillGroupController::class)->except('show')
            ->parameters(['skills' => 'skillGroup']);
        Route::patch('skills/{skillGroup}/move/{direction}', [SkillGroupController::class, 'move'])
            ->whereIn('direction', ['up', 'down'])->name('skills.move');

        Route::get('guestbook', [AdminGuestbookController::class, 'index'])->name('guestbook.index');
        Route::patch('guestbook/{entry}/approve', [AdminGuestbookController::class, 'approve'])->name('guestbook.approve');
        Route::patch('guestbook/{entry}/unapprove', [AdminGuestbookController::class, 'unapprove'])->name('guestbook.unapprove');
        Route::delete('guestbook/{entry}', [AdminGuestbookController::class, 'destroy'])->name('guestbook.destroy');

        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::patch('bookings/{booking}/approve', [AdminBookingController::class, 'approve'])->name('bookings.approve');
        Route::patch('bookings/{booking}/decline', [AdminBookingController::class, 'decline'])->name('bookings.decline');
        Route::get('availability', [AvailabilityController::class, 'edit'])->name('availability.edit');
        Route::put('availability/settings', [AvailabilityController::class, 'updateSettings'])->name('availability.settings');
        Route::post('availability/rules', [AvailabilityController::class, 'storeRule'])->name('availability.rules.store');
        Route::delete('availability/rules/{rule}', [AvailabilityController::class, 'destroyRule'])->name('availability.rules.destroy');
        Route::post('availability/blocked', [AvailabilityController::class, 'storeBlocked'])->name('availability.blocked.store');
        Route::delete('availability/blocked/{blockedDate}', [AvailabilityController::class, 'destroyBlocked'])->name('availability.blocked.destroy');

        Route::get('store-checks', [StoreCheckController::class, 'index'])->name('store-checks.index');

        Route::get('subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
        Route::get('subscribers/export', [SubscriberController::class, 'export'])->name('subscribers.export');
        Route::delete('subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy');

        Route::get('contacts', [ContactMessageController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contact}', [ContactMessageController::class, 'show'])->name('contacts.show');
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
