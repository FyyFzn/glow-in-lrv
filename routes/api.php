<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authenticated user's own profile & public profiles
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update']);
    Route::get('/users/{user:username}', [ProfileController::class, 'showUser']);

    // Chat API routes
    Route::get('/chat-users', [ChatController::class, 'users']);
    Route::apiResource('messages', ChatController::class);
});

// API routes handle the actual data transactions
Route::apiResource('posts', PostController::class)
    ->names('api.posts')
    ->middleware('auth:sanctum');

// Comments API routes
Route::apiResource('comments', CommentController::class);