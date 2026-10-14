<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Middleware\ProductWriteRateLimit;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')
    ->middleware('throttle:auth')
    ->group(function () {
        Route::post('/register', [
            AuthController::class,
            'register',
        ])->name('auth.register');

        Route::post('/login', [
            AuthController::class,
            'login',
        ])->name('auth.login');
    });

    
Route::prefix('products')
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', [ProductController::class, 'index'])
            ->name('products.index');

        Route::get('/{id}', [ProductController::class, 'show'])
            ->whereNumber('id')
            ->name('products.show');

        Route::post('/', [ProductController::class, 'store'])
            ->middleware(ProductWriteRateLimit::class)
            ->name('products.store');

        Route::put('/{id}', [ProductController::class, 'update'])
            ->whereNumber('id')
            ->middleware(ProductWriteRateLimit::class)
            ->name('products.update');

        Route::delete('/{id}', [ProductController::class, 'destroy'])
            ->whereNumber('id')
            ->middleware(ProductWriteRateLimit::class)
            ->name('products.destroy');
    });