<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SerialController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Аутентификация
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Пользователь
Route::patch('/user', [UserController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::get('/user/shows', [UserController::class, 'watchlist'])->middleware('auth:sanctum');
// Route::get('/user/shows', [UserController::class, 'watchlist']);

// Сериалы
Route::get('/shows', [SerialController::class, 'index']);
Route::get('/shows/{id}', [SerialController::class, 'show']);
Route::post('/user/shows/watch/{id}', [SerialController::class, 'addToWatchlist'])->middleware('auth:sanctum');
Route::delete('/user/shows/watch/{id}', [SerialController::class, 'removeFromWatchlist'])->middleware('auth:sanctum');
Route::post('/user/shows/{id}/vote', [SerialController::class, 'vote'])->middleware('auth:sanctum');
Route::post('/shows', [SerialController::class, 'addToDataBase'])->middleware('auth:sanctum');

// Жанры
Route::get('/genres', [GenreController::class, 'index']);
Route::patch('/genres/{genre}', [GenreController::class, 'update'])
  ->middleware('auth:sanctum')
  ->name('genres.update');

// Эпизоды
Route::get('/shows/{showId}/episodes', [EpisodeController::class, 'index']);
Route::get('/episode/{id}', [EpisodeController::class, 'show']);
Route::post('/user/episodes/watch/{id}', [EpisodeController::class, 'markAsWatched'])->middleware('auth:sanctum');
Route::delete('/user/episodes/watch/{id}', [EpisodeController::class, 'removeFromWatched'])->middleware('auth:sanctum');
Route::get('/user/shows/{showId}/new-episodes', [EpisodeController::class, 'getUnwatchedEpisodes'])->middleware('auth:sanctum');

// Комментарии
Route::get('/episode/{episodeId}/comments', [CommentController::class, 'index']);
Route::post('/episode/{episodeId}/comments/{commentId?}', [CommentController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/comments/{commentId}', [CommentController::class, 'destroy'])
  ->middleware('auth:sanctum')
  ->name('comments.destroy');
