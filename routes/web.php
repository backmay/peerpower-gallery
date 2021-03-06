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

Route::middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\ImageController::class, 'index'])->name('index');
    Route::post('store', [App\Http\Controllers\ImageController::class, 'store'])->name('store');
    Route::get('list', [App\Http\Controllers\ImageController::class, 'list'])->name('list');
    Route::delete('image/{id}', [App\Http\Controllers\ImageController::class, 'destroy'])->name('destroy');

});
