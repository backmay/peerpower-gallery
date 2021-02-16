<?php

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

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('main', [App\Http\Controllers\ImageController::class, 'index'])->name('home');
    Route::post('store', [App\Http\Controllers\ImageController::class, 'store'])->name('gallery.store');
    Route::get('list', [App\Http\Controllers\ImageController::class, 'list'])->name('gallery.lists');
});
