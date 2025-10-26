<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnalyticsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/vending', function () {
    return view('vending');
});

Route::get('/admin', function () {
    return view('admin');
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    // Public routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    
    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::post('/login', [AdminController::class, 'login']);
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/products', [ProductController::class, 'index']);
            Route::post('/products', [ProductController::class, 'store']);
            Route::put('/products/{product}', [ProductController::class, 'update']);
            Route::delete('/products/{product}', [ProductController::class, 'destroy']);
            
            Route::get('/orders', [OrderController::class, 'index']);
            
            Route::get('/analytics', [AnalyticsController::class, 'index']);
        });
    });
});
