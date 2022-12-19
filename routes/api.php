<?php

use App\Http\Controllers\Api\{CategoryController};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => response()->json(['success' => true]));
Route::apiResource('categories', CategoryController::class);
