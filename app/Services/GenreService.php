<?php

namespace App\Services;

use App\Models\Genre;

class GenreService
{
  /**
   * Получает все жанры с пагинацией.
   *
   * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
   */
  public function getAllGenres()
  {
    return Genre::paginate();
  }

  /**
   * Обновляет жанр переданными валидированными данными.
   *
   * @param Genre $genre
   * @param array $data
   * @return Genre
   */
  public function updateGenre(Genre $genre, array $data): Genre
  {
    $genre->update($data);
    return $genre;
  }
}
