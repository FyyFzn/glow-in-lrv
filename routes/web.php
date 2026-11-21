<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])
    ->middleware(['auth'])
    ->name('home');

Route::middleware('auth')->group(function () {
    // Post routes
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Detail alias
    Route::get('/detail', function (Request $request) {
        $postId = $request->query('id', $request->query('post_id', 1));
        $post = Post::with(['user', 'comments.user'])->withCount('comments')->find($postId);

        if (! $post) {
            $post = Post::firstOrCreate(
                ['id' => 1],
                [
                    'user_id' => auth()->id() ?? 1,
                    'content' => 'Mock Post: This post serves as the host for demonstrating the Comment CRUD API and Frontend.',
                ]
            );
            $post->load(['user', 'comments.user'])->loadCount('comments');
        }

        return view('comments.detail', compact('post'));
    })->name('detail');

    // Public profile pages
    Route::get('/u/{user:username}', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/u/{user:username}/follow', [ProfileController::class, 'follow'])->name('profile.follow');
    Route::delete('/u/{user:username}/follow', [ProfileController::class, 'unfollow'])->name('profile.unfollow');

    // Breeze default profile settings routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chat routes
    Route::get('/chat', function () {
        return view('chat.index');
    })->name('chat.index');
    Route::get('/chat/{userId}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{userId}', [ChatController::class, 'store'])->name('chat.store');
    Route::put('/chat/message/{id}', [ChatController::class, 'update'])->name('chat.update');
    Route::delete('/chat/message/{id}', [ChatController::class, 'destroy'])->name('chat.destroy');
});

Route::redirect('/dashboard', '/')->name('dashboard');

require __DIR__.'/auth.php';