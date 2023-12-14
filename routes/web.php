<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::resource('/product', ProductController::class);

//extra product. routes
Route::prefix('/product')->name('product.')->group(function () {
    //two possible way of obtaining Products via keywords, one via url_encoded text and the other via GET filter
    Route::get('/search/keyword',[ProductController::class,'search'])->name('search');
    Route::get('/search/keyword/{keyword}',[ProductController::class,'searchViaPath'])->name('searchByUri');
});

Route::get('/', function () {
    return view('welcome');
});
