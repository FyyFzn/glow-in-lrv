<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// API routes handle the actual data transactions
Route::apiResource('posts', PostController::class)->middleware('auth:sanctum');
