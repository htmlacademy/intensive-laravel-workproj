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
Route::get('/csrf-token', function () {
    return response()->json(null, 204);
})->middleware('web');

Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');

Route::get('/genres', [\App\Http\Controllers\GenreController::class, 'index'])->name('genres.index');
Route::get('/shows', [\App\Http\Controllers\ShowController::class, 'index'])->name('shows.index');
Route::get('/shows/{show}', [\App\Http\Controllers\ShowController::class, 'show'])->name('shows.show');
Route::get('/shows/{show}/episodes', [\App\Http\Controllers\EpisodeController::class, 'index'])->name('episodes.index');
Route::get('/episode/{episode}', [\App\Http\Controllers\EpisodeController::class, 'show'])->name('episodes.show');
Route::get('/episode/{episode}/comments', [\App\Http\Controllers\CommentController::class, 'index'])->name('comments.index');

Route::prefix('user')->name('user.')->middleware('auth:sanctum')->group(function () {
    Route::get('/shows', [\App\Http\Controllers\UserController::class, 'shows'])->name('shows.index');
    Route::get('/shows/{show}/new-episodes', [\App\Http\Controllers\UserController::class, 'newEpisodes'])->name('shows.new-episodes');
});
