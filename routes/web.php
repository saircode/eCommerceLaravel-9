<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\userController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',
])->prefix('tienda')->group(function () {
    Route::get('/', [ShopController::class, 'index'] )->name('shop.index');
});

Route::middleware(['auth:sanctum','userAdmin',config('jetstream.auth_session')])->prefix('productos')
->group(function () {
    Route::get('/', [ProductController::class , 'index'])->name('products.index');
    Route::post('/', [ProductController::class , 'store'])->name('products.create');
    Route::post('/img-product', [ProductController::class , 'storeImg'])->name('products.save.image');
    Route::put('/', [ProductController::class, 'update'])->name("products.update");
    Route::delete('/{id}', [ProductController::class , 'destroy'])->name('products.delete');
});


Route::middleware(['auth:sanctum',config('jetstream.auth_session')])->prefix('carrito')
->group(function () {
    Route::get('/', [ShoppingCartController::class , 'index'])->name('shoppingcart.index');
    Route::post('/', [ShoppingCartController::class , 'store'])->name('shoppingcart.add');
    Route::delete('/{id}', [ShoppingCartController::class , 'destroy'])->name('shoppingcart.delete');
});

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',
])->prefix('compras')->group(function () {
    Route::get('/', [PurchaseController::class, 'index'] )->name('purchase.index');
    Route::post('/', [PurchaseController::class, 'store'] )->name('purchase.store');
});

Route::middleware(['auth:sanctum','userAdmin',config('jetstream.auth_session'),'verified',
])->prefix('usuarios')->group(function () {
    Route::get('/', [userController::class, 'index'] )->name('users.index');
    Route::post('/', [userController::class, 'store'] )->name('users.create');
    Route::put('/', [userController::class, 'update'] )->name('users.update');
    Route::delete('/{id}', [userController::class, 'destroy'] )->name('users.delete');
});