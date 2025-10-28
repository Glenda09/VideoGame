<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlatformController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:super_admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('products', ProductController::class)->except(['show']);
        Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
            ->name('products.toggle-status');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('platforms', PlatformController::class)->except(['show']);
        Route::resource('users', UserController::class)->only(['index', 'update']);
    });
