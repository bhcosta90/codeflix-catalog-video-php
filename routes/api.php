<?php

use App\Http\Controllers\Api\{CategoryController, GenreController};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => response()->json(['success' => true]));
Route::apiResource('categories', CategoryController::class);
Route::apiResource('genres', GenreController::class);
