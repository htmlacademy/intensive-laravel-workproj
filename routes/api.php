<?php

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

Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');

Route::get('/genres', [\App\Http\Controllers\GenreController::class, 'index'])->name('genres.index');
Route::get('/shows', [\App\Http\Controllers\ShowController::class, 'index'])->name('shows.index');
Route::get('/shows/{show}', [\App\Http\Controllers\ShowController::class, 'show'])->name('shows.show');
Route::get('/shows/{show}/episodes', [\App\Http\Controllers\EpisodeController::class, 'index'])->name('episodes.index');
Route::get('/episode/{episode}', [\App\Http\Controllers\EpisodeController::class, 'show'])->name('episodes.show');
Route::get('/episode/{episode}/comments', [\App\Http\Controllers\CommentController::class, 'index'])->name('comments.index');
Route::post('/episode/{episode}/comments/{comment?}', [\App\Http\Controllers\CommentController::class, 'store'])->middleware('auth:sanctum')->name('comments.store');

Route::prefix('user')->name('user.')->middleware('auth:sanctum')->group(function () {
    Route::patch('/', [\App\Http\Controllers\UserController::class, 'update'])->name('update');
    Route::get('/shows', [\App\Http\Controllers\UserController::class, 'shows'])->name('shows.index');
    Route::get('/shows/{show}/new-episodes', [\App\Http\Controllers\UserController::class, 'newEpisodes'])->name('shows.new-episodes');
    Route::post('/shows/watch/{show}', [\App\Http\Controllers\UserController::class, 'watchShow'])->name('shows.watch');
    Route::delete('/shows/watch/{show}', [\App\Http\Controllers\UserController::class, 'unwatchShow'])->name('shows.unwatch');
    Route::post('/episodes/watch/{episode}', [\App\Http\Controllers\UserController::class, 'watchEpisode'])->name('episodes.watch');
    Route::delete('/episodes/watch/{episode}', [\App\Http\Controllers\UserController::class, 'unwatchEpisode'])->name('episodes.unwatch');
    Route::post('/shows/{show}/vote', [\App\Http\Controllers\UserController::class, 'vote'])->name('shows.vote');
});
