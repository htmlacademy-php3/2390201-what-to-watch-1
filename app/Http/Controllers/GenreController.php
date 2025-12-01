<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;

class GenreController extends Controller
{
  // Получение списка жанров
  public function index(): BaseResponse
  {
    try {
      $data = Genre::all();
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Обновление жанра
  public function update(Request $request, Genre $genre): BaseResponse
  {
    try {
      // Проверка права через гейт
      if (!Gate::allows('update-genre')) {
        return new FailResponse([], 'Недостаточно прав для редактирования жанра.', 403);
      }

      $validated = $request->validate([
        'title' => 'required|string|max:255',
        'imdb_id' => 'nullable|string|max:255',
      ]);

      $genre->update($validated);

      return new SuccessResponse($genre);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage(), 500);
    }
  }
}
