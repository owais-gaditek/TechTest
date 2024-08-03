<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FibonacciController;
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

Route::get('/articles', [ArticleController::class, 'indexView']);
Route::get('/fibonacci', [FibonacciController::class, 'index'])->name('fibonacci.index');
Route::post('/fibonacci', [FibonacciController::class, 'calculate'])->name('fibonacci.calculate');
