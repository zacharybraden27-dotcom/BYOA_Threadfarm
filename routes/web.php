<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\TweetController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('posts.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    
    // Blog posts
    Route::get('/posts/archived', [BlogPostController::class, 'archived'])->name('posts.archived');
    Route::resource('posts', BlogPostController::class);
    Route::post('/posts/{post}/generate-tweets', [BlogPostController::class, 'generateTweets'])->name('posts.generate-tweets');
    Route::post('/posts/{post}/archive', [BlogPostController::class, 'archive'])->name('posts.archive');
    Route::post('/posts/{post}/unarchive', [BlogPostController::class, 'unarchive'])->name('posts.unarchive');
    
    // Tweets
    Route::put('/tweets/{tweet}', [TweetController::class, 'update'])->name('tweets.update');
    Route::post('/tweets/{tweet}/mark-posted', [TweetController::class, 'markAsPosted'])->name('tweets.mark-posted');
    Route::post('/tweets/{tweet}/discard', [TweetController::class, 'discard'])->name('tweets.discard');
    Route::post('/tweets/{tweet}/restore', [TweetController::class, 'restore'])->name('tweets.restore');
});
