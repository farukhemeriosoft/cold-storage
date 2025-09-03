<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BasketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Customer management routes
    Route::apiResource('customers', CustomerController::class);
    Route::patch('/customers/{customer}/deactivate', [CustomerController::class, 'deactivate']);
    Route::patch('/customers/{customer}/activate', [CustomerController::class, 'activate']);
    Route::get('/customers/{customer}/baskets', [CustomerController::class, 'baskets']);

    // Basket management routes
    Route::get('/batches', [BasketController::class, 'index']);
    Route::post('/batches', [BasketController::class, 'createBatch']);
    Route::post('/batches/{batch}/baskets', [BasketController::class, 'addBasketsToBatch']);
    Route::post('/batches/{batch}/dispatch', [BasketController::class, 'dispatchBatch']);
    Route::post('/baskets/dispatch', [BasketController::class, 'dispatch']);
});
