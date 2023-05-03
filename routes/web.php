<?php

use App\Http\Controllers\ProductController;
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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


Route::middleware(['auth:sanctum',config('jetstream.auth_session')])->prefix('productos')
    ->group(function () {
        Route::get('/', [ProductController::class , 'index'])->name('products.index');
        Route::post('/', [ProductController::class , 'store'])->name('products.create');
        Route::post('/img-product', [ProductController::class , 'storeImg'])->name('products.save.image');
        Route::put('/', [ProductController::class, 'update'])->name("products.update");
        Route::delete('/', [ProductController::class , 'destroy'])->name('products.delete');
    });

// Route::resource('productos', ProductController::class)
//     ->middleware(['auth:sanctum',config('jetstream.auth_session')]);
