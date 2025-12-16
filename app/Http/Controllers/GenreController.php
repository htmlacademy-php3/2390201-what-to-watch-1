<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateGenreRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Genre;
use App\Services\GenreService;
use Illuminate\Support\Facades\Gate;

class GenreController extends Controller
{
  /**
   * Инициализация контроллера с добавлением экземпляра сервиса для работы с жанрами.
   *
   * @param GenreService $genreService
   */
  public function __construct(
    private readonly \App\Services\GenreService $genreService
  ) {}

  /**
   * Получение списка жанров.
   *
   * @return BaseResponse
   */
  public function index(): BaseResponse
  {
    $genres = $this->genreService->getAllGenres();
    return new SuccessResponse($genres->items());
  }

  /**
   * Обновление жанра.
   *
   * @param UpdateGenreRequest $request
   * @param Genre $genre
   * @return BaseResponse
   */
  public function update(UpdateGenreRequest $request, Genre $genre): BaseResponse
  {
    // Проверка права через Gate (возвращает true/false)
    if (!Gate::allows('update-genre')) {
      return new FailResponse([], 'Неавторизованное действие.', 403);
    }

    $validated = $request->validated();
    $updatedGenre = $this->genreService->updateGenre($genre, $validated);

    return new SuccessResponse([$updatedGenre]);
  }
}
