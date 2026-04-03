<?php

use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\CommentReportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EpisodeController;
use App\Http\Controllers\Admin\FavoriteController as AdminFavoriteController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\ImportLogController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SearchLogController;
use App\Http\Controllers\Admin\SeasonController;
use App\Http\Controllers\Admin\TitleController;
use App\Http\Controllers\Admin\TitleRequestController;
use App\Http\Controllers\Admin\TitleViewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TitleRequestController as PublicTitleRequestController;
use App\Http\Controllers\WatchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/movies', [MoviesController::class, 'index'])->name('movies');
Route::get('/series', [SeriesController::class, 'index'])->name('series');
Route::get('/watch/{slug}', [WatchController::class, 'show'])->name('watch');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');
Route::post('/search/log', [SearchController::class, 'log'])->name('search.log');

// Comments (public read)
Route::get('/comments/{titleId}', [CommentController::class, 'index'])->name('comments.index');

// View tracking (fire-and-forget from JS)
Route::post('/track-view', [WatchController::class, 'trackView'])->name('track-view');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Favorites
    Route::get('/my-list', [FavoriteController::class, 'index'])->name('my-list');
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Title requests
    Route::get('/request-title', [PublicTitleRequestController::class, 'create'])->name('title-requests.create');
    Route::post('/request-title', [PublicTitleRequestController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('title-requests.store');
    Route::get('/my-requests', [PublicTitleRequestController::class, 'index'])->name('my-requests');

    // Comments (auth actions)
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/report', [CommentController::class, 'report'])->name('comments.report');
});

// Signed URL for email verification
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin,moderator'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('comments', AdminCommentController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::patch('comments/{comment}/approve', [AdminCommentController::class, 'toggleApproval'])->name('comments.approve');

        Route::resource('titles', TitleController::class)->except(['show']);
        Route::resource('seasons', SeasonController::class)->except(['show']);
        Route::resource('episodes', EpisodeController::class)->except(['show']);
        Route::resource('reports', CommentReportController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::resource('title-requests', TitleRequestController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::resource('search-logs', SearchLogController::class)->only(['index', 'show', 'destroy']);
        Route::resource('title-views', TitleViewController::class)->only(['index', 'show', 'destroy']);
        Route::resource('import-logs', ImportLogController::class)->only(['index', 'show', 'destroy']);

        Route::middleware('role:admin')->group(function () {
            Route::resource('users', UserController::class)->except(['show']);
            Route::resource('roles', RoleController::class)->except(['show']);
            Route::resource('genres', GenreController::class)->except(['show']);
            Route::resource('favorites', AdminFavoriteController::class)->except(['show']);
        });
    });
