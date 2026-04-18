<?php

use App\Http\Controllers\Admin\AdSlotController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DownloadLinkController;
use App\Http\Controllers\Admin\IpBlockController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\RedirectController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SitemapController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\CommentController;
use App\Http\Controllers\Front\DownloadController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\PostController;
use App\Http\Controllers\Front\RatingController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Front\TagController;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\SitemapController as FrontSitemapController;
use Illuminate\Support\Facades\Route;

// Authentication routes (admin only)
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.post')->middleware('throttle:5,15');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Posts
    Route::resource('posts', AdminPostController::class);
    Route::post('posts/{post}/publish', [AdminPostController::class, 'publish'])->name('posts.publish');
    Route::post('posts/{post}/gallery', [AdminPostController::class, 'addGalleryImages'])->name('posts.gallery.add');
    Route::delete('posts/{post}/gallery/{index}', [AdminPostController::class, 'removeGalleryImage'])->name('posts.gallery.remove');
    Route::delete('posts/{post}/image/{field}', [AdminPostController::class, 'removeImage'])->name('posts.image.remove');

    // Categories
    Route::resource('categories', AdminCategoryController::class);

    // Tags
    Route::resource('tags', AdminTagController::class);

    // Comments
    Route::get('comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::post('comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::post('comments/{comment}/reject', [AdminCommentController::class, 'reject'])->name('comments.reject');
    Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('comments/bulk', [AdminCommentController::class, 'bulk'])->name('comments.bulk');

    // Media
    Route::get('media', [AdminMediaController::class, 'index'])->name('media.index');
    Route::post('media/upload', [AdminMediaController::class, 'upload'])->name('media.upload');
    Route::delete('media/{media}', [AdminMediaController::class, 'destroy'])->name('media.destroy');
    Route::put('media/{media}/alt', [AdminMediaController::class, 'updateAlt'])->name('media.alt');

    // Download Links
    Route::get('download-links', [DownloadLinkController::class, 'index'])->name('download-links.index');
    Route::post('download-links', [DownloadLinkController::class, 'store'])->name('download-links.store');
    Route::put('download-links/{link}', [DownloadLinkController::class, 'update'])->name('download-links.update');
    Route::delete('download-links/{link}', [DownloadLinkController::class, 'destroy'])->name('download-links.destroy');
    Route::post('download-links/reorder', [DownloadLinkController::class, 'reorder'])->name('download-links.reorder');

    // Ad Slots (admin only)
    Route::resource('ad-slots', AdSlotController::class)->middleware('admin.role');

    // SEO
    Route::get('seo', [SeoController::class, 'index'])->name('seo.index')->middleware('admin.role');
    Route::post('seo', [SeoController::class, 'update'])->name('seo.update')->middleware('admin.role');

    // Sitemap
    Route::get('sitemap', [SitemapController::class, 'index'])->name('sitemap.index')->middleware('admin.role');
    Route::post('sitemap/generate', [SitemapController::class, 'generate'])->name('sitemap.generate')->middleware('admin.role');
    Route::post('sitemap/ping', [SitemapController::class, 'ping'])->name('sitemap.ping')->middleware('admin.role');
    Route::put('sitemap/settings', [SitemapController::class, 'settings'])->name('sitemap.settings')->middleware('admin.role');

    // Redirects (admin only)
    Route::resource('redirects', RedirectController::class)->middleware('admin.role');

    // IP Block (admin only)
    Route::get('ip-blocks', [IpBlockController::class, 'index'])->name('ip-blocks.index')->middleware('admin.role');
    Route::post('ip-blocks', [IpBlockController::class, 'store'])->name('ip-blocks.store')->middleware('admin.role');
    Route::delete('ip-blocks/{ip}', [IpBlockController::class, 'destroy'])->name('ip-blocks.destroy')->middleware('admin.role');

    // Users (admin only)
    Route::resource('users', UserController::class)->middleware('admin.role');

    // Settings (admin only)
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index')->middleware('admin.role');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('admin.role');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Profile (all authenticated admins)
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');

    // AI / Gemini
    Route::post('ai/generate', [GeminiController::class, 'generate'])->name('ai.generate');
});

// Sitemaps
Route::get('/sitemap.xml', [FrontSitemapController::class, 'index']);
Route::get('/sitemap-posts.xml', [FrontSitemapController::class, 'posts']);
Route::get('/sitemap-categories.xml', [FrontSitemapController::class, 'categories']);
Route::get('/sitemap-pages.xml', [FrontSitemapController::class, 'pages']);

// Frontend public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Search (noindex)
Route::get('/search', [SearchController::class, 'index'])->name('search')->middleware('throttle:30,1');
Route::get('/search/autocomplete', [SearchController::class, 'autocomplete'])->name('search.autocomplete')->middleware('throttle:30,1');

// Static pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');

// Category pages
Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category.show');

// Tag pages (noindex)
Route::get('/tag/{tag:slug}', [TagController::class, 'show'])->name('tag.show');

// Download timer (noindex, new tab)
Route::get('/download/{post:slug}', [DownloadController::class, 'show'])->name('download.show');
Route::post('/download/click', [DownloadController::class, 'trackClick'])
    ->name('download.click')
    ->middleware('throttle:10,1');

// Ratings
Route::post('/rate', [RatingController::class, 'store'])->name('rate.store')->middleware('throttle:5,1');

// Comments
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('throttle:3,60');

// Post detail page (must be last — catch-all slug)
Route::get('/{post:slug}', [PostController::class, 'show'])->name('post.show');
