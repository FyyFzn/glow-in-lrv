<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Authenticated user's own profile
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update']);

    // Any user's public profile
    Route::get('/users/{user:username}', [ProfileController::class, 'showUser']);
});


// API routes handle the actual data transactions
// Namespaced route names so they do not collide with the web posts.* routes.
Route::apiResource('posts', PostController::class)
    ->names('api.posts')
    ->middleware('auth:sanctum');
