<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SeoController;
use App\Livewire\Admin;
use Illuminate\Support\Facades\Route;

Route::controller(PageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/story', 'story')->name('story');
    Route::get('/help', 'help')->name('help');
    Route::get('/updates', 'updates')->name('updates.index');
    Route::get('/updates/{post}', 'update')->name('updates.show');
    Route::get('/transparency', 'transparency')->name('transparency');
    Route::get('/prayer-wall', 'prayers')->name('prayers');
    Route::get('/contact', 'contact')->name('contact');
});

Route::get('/lang/{locale}', LocaleController::class)->whereIn('locale', ['en', 'fil'])->name('locale');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots']);

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'show'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', Admin\Dashboard::class)->name('dashboard');
        Route::get('/fundraising', Admin\Fundraising::class)->name('fundraising');
        Route::get('/updates', Admin\Posts::class)->name('posts');
        Route::get('/transparency', Admin\Expenses::class)->name('expenses');
        Route::get('/prayers', Admin\Prayers::class)->name('prayers');
        Route::get('/donations', Admin\DonationDetails::class)->name('donations');
        Route::get('/needs', Admin\Needs::class)->name('needs');
        Route::get('/shop', Admin\ShopItems::class)->name('shop');
        Route::get('/messages', Admin\Messages::class)->name('messages');
        Route::get('/story', Admin\StoryContent::class)->name('story');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});
