<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/hi', function () {
    echo 'hello';
});

Route::get('/demo', [ProductController::class, 'demo'])->name('product.demo');

Route::group(['prefix' => 'api'], function () {
    Route::get('/products', [ProductController::class, 'indexApi'])->name('product.indexApi');
    Route::post('/cart/add', [CartController::class, 'addApi'])->name('cart.addApi');
    Route::post('/cart/del', [CartController::class, 'delApi'])->name('cart.delApi');

    Route::post('/cart/add/{itemId}/email/{email}', [CartController::class, 'addOneApi'])->name('cart.addOneApi');
    Route::post('/cart/del/{itemId}/email/{email}', [CartController::class, 'delOneApi'])->name('cart.delOneApi');
});


/**
 * Verb             URI                     Action  Route Name
 * GET              /photos                 index   photos.index
 * GET              /photos/create          create  photos.create
 * POST             /photos                 store   photos.store
 * GET              /photos/{photo}         show    photos.show
 * GET              /photos/{photo}/edit    edit    photos.edit
 * PUT/PATCH        /photos/{photo}         update  photos.update
 * DELETE           /photos/{photo}         destroy photos.destroy
 */
