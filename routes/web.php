<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Storefront\CatalogController;
use App\Http\Controllers\Storefront\LivePriceController;
use App\Http\Controllers\Storefront\ProductController as StorefrontProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('storefront.catalog');
Route::redirect('/shop', '/', 301)->name('storefront.shop.redirect');
Route::get('/catalog/products', [CatalogController::class, 'load'])->name('storefront.catalog.load');
Route::get('/market/live-prices', LivePriceController::class)
    ->middleware('throttle:120,1')->name('storefront.live-prices');
Route::get('/p/{product}', [StorefrontProductController::class, 'show'])
    ->whereNumber('product')->name('storefront.products.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'admin.active'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::resource('admins', AdminController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class);
});
