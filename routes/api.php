<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\customer\ProductController as CustomerProductController;
use App\Http\Controllers\customer\UserMeasurementController;
use App\Http\Controllers\customer\UserPreferenceController;
use App\Http\Controllers\customer\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\vendor\ProductController;
use App\Http\Controllers\vendor\PromotionController;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('/customer/payment-success', [OrderController::class, 'paymentSuccess']);
Route::get('/customer/payment-cancel', [OrderController::class, 'paymentCancel']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('brands', [BrandController::class, 'index']);
    Route::prefix('/vendor')->group(function () {
        Route::apiResource('products', ProductController::class);
        Route::apiResource('promotions', PromotionController::class);
        Route::get('product/search', [ProductController::class, 'search']);
    });
    Route::prefix('customer/')->group(function () {
        Route::apiResource('user-measurements', UserMeasurementController::class);
        Route::apiResource('user-preference', UserPreferenceController::class);
        Route::get('products', [CustomerProductController::class, 'index']);
        Route::get('products/{id}', [CustomerProductController::class, 'show']);
        Route::get('product/search', [CustomerProductController::class, 'search']);
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders/{id}', [OrderController::class, 'show']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::post('orders/pay/{id}', [OrderController::class, 'pay']);
    });
});
