<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/products-data', [ProductController::class, 'getProductsData'])->name('products.data');
    Route::post('/products/{id}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    Route::get('/pos/products', [ProductController::class, 'consume'])->name('products.pos');
    Route::resource('products', ProductController::class);

    Route::resource('orders', OrderController::class);

    Route::get('/category-data', [CategoryController::class, 'getCategoryData'])->name('category.data');
    Route::post('/category/{id}/duplicate', [CategoryController::class, 'duplicate'])->name('category.duplicate');
    Route::resource('category', CategoryController::class);

    Route::get('/units-data', [UnitsController::class, 'getUnitsData'])->name('units.data');
    Route::post('/units/{id}/duplicate', [UnitsController::class, 'duplicate'])->name('units.duplicate');
    Route::resource('units', UnitsController::class);

    Route::get('/users-data', [UsersController::class, 'getUsersData'])->name('users.data');
    Route::resource('users', UsersController::class);
});

require __DIR__ . '/auth.php';
