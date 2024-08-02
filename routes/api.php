<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::apiResource('articles', ArticleController::class);

// // Route::post('/test-upload', [TestController::class, 'upload']);
// Route::put('/api/articles/{id}', [ArticleController::class, 'update']);

// Route::put('/test', [TestController::class, 'handle']);

// Route::put('/test-simple', function (Request $request) {
//     return response()->json($request->all());
// });
// Route::put('/test', function (Request $request) {
//     return response()->json([
//         'title' => $request->input('title'),
//         'content' => $request->input('content'),
//     ]);
// });