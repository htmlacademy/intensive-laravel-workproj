<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/genres', [\App\Http\Controllers\GenreController::class, 'index'])->name('genres.index');
Route::get('/shows', [\App\Http\Controllers\ShowController::class, 'index'])->name('shows.index');
Route::get('/shows/{show}', [\App\Http\Controllers\ShowController::class, 'show'])->name('shows.show');
Route::get('/shows/{show}/episodes', [\App\Http\Controllers\EpisodeController::class, 'index'])->name('episodes.index');
Route::get('/episode/{episode}', [\App\Http\Controllers\EpisodeController::class, 'show'])->name('episodes.show');
Route::get('/episode/{episode}/comments', [\App\Http\Controllers\CommentController::class, 'index'])->name('comments.index');

