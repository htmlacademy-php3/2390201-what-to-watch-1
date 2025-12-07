<?php

namespace App\Services;

use App\Models\Serial;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SerialService
{
  /**
   * Получение списка сериалов с фильтрацией, сортировкой и пагинацией
   *
   * @param array $params Параметры запроса (page, order_by, order_to, genre, search)
   * @param int $perPage Количество элементов на странице
   * @return array Структура данных согласно API
   */
  public function getSerialsList(array $params, int $perPage): array
  {
    $page = $params['page'] ?? 1;

    $query = Serial::query()->with('genres');

    // Применяем фильтрацию по жанру
    $this->applyGenreFilter($query, $params);

    // Применяем поиск по названию
    $this->applySearchFilter($query, $params);

    // Применяем сортировку
    $this->applySorting($query, $params);

    // Получаем пагинированный результат
    $paginator = $query->paginate($perPage, ['*'], 'page', $page);

    // Форматируем данные для ответа согласно API
    return $this->formatSeveralSerialsResponse($paginator);
  }

  /**
   * Получение детальной информации о сериале
   *
   * @param int $id ID сериала
   * @return array Данные сериала согласно API
   */
  public function getSerialDetails(int $id): array
  {
    $serial = Serial::with([
        'genres',
        'seasons.episodes',
        'serialWatchingRecords',
        'votes'
    ])->findOrFail($id);

    return $this->formatSerialResponse($serial);
  }

  /**
   * Применение фильтрации по жанру к запросу
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param array $params
   * @return void
   */
  private function applyGenreFilter($query, array $params): void
  {
    if (!empty($params['genre'])) {
      $query->whereHas('genres', function ($q) use ($params) {
        $q->where('title', 'like', '%' . $params['genre'] . '%');
      });
    }
  }

  /**
   * Применение поиска по названию к запросу
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param array $params
   * @return void
   */
  private function applySearchFilter($query, array $params): void
  {
    if (!empty($params['search'])) {
      $search = $params['search'];
      $query->where(function ($q) use ($search) {
        $q->where('title', 'like', '%' . $search . '%')
          ->orWhere('title_original', 'like', '%' . $search . '%');
      });
    }
  }

  /**
   * Применение сортировки к запросу
   *
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param array $params
   * @return void
   */
  private function applySorting($query, array $params): void
  {
    if (!empty($params['order_by'])) {
      $orderDirection = $params['order_to'] ?? 'desc';

      if ($params['order_by'] === 'date') {
        $query->orderBy('year', $orderDirection);
      } elseif ($params['order_by'] === 'rating') {
        // Для сортировки по рейтингу используем оптимизированный подзапрос
        $query->leftJoin('serials_votes', 'serials.id', '=', 'serials_votes.serial_id')
          ->select('serials.*', DB::raw('COALESCE(AVG(serials_votes.vote), 0) as avg_rating'))
          ->groupBy('serials.id')
          ->orderBy('avg_rating', $orderDirection);
      }
    } else {
      // Сортировка по умолчанию
      $query->orderBy('id', 'desc');
    }
  }

  /**
   * Форматирование списка сериалов согласно API
   *
   * @param LengthAwarePaginator $paginator
   * @return array
   */
  private function formatSeveralSerialsResponse(LengthAwarePaginator $paginator): array
  {
    $data = $paginator->items();

    // Форматируем каждый элемент используя общий метод
    return array_map(function ($serial) {
      return $this->formatSerialResponse($serial);
    }, $data);
  }

  /**
   * Форматирование одного сериала согласно API
   *
   * @param Serial $serial
   * @return array
   */
  private function formatSerialResponse(Serial $serial): array
  {
    return [
      'id' => $serial->id,
      'title' => $serial->title,
      'title_original' => $serial->title_original,
      'status' => $serial->status ?? '',
      'year' => $serial->year ? $serial->year->year : 0,
      'rating' => $serial->rating,
      'total_seasons' => $serial->total_seasons,
      'total_episodes' => $serial->total_episodes,
      'genres' => $serial->genres->map(function ($genre) {
        return [
          'id' => $genre->id,
          'title' => $genre->title
        ];
      })->toArray(),
      'watch_status' => $serial->watch_status,
      'watched_episodes' => $serial->watched_episodes,
      'user_vote' => $serial->user_vote,
    ];
  }
}
