<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Product\ProductTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'OK';
});

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('product-types', ProductTypeController::class);
});
