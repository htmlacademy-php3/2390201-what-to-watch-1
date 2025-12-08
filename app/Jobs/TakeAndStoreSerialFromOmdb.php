<?php

namespace App\Jobs;

use App\Models\Serial;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Romnosk\Repository\OMDBRepositoryInterface;
use Illuminate\Support\Facades\Log;

/**
 * Фоновая задача для получения данных о сериале по IMDB ID через OMDB API
 * и сохранения базовой информации в таблицу serials.
 *
 * @see Serial
 * @see OMDBRepositoryInterface
 */
class TakeAndStoreSerialFromOmdb implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * IMDB ID сериала, который необходимо загрузить и сохранить.
   *
   * @var string
   */
  public string $imdbId;

  /**
   * Создаёт новый экземпляр задачи.
   *
   * @param string $imdbId IMDB ID фильма/сериала.
   */
  public function __construct(string $imdbId)
  {
    $this->imdbId = $imdbId;
  }

  /**
   * Выполняет логику задачи: получает данные от OMDB API и сохраняет сериал.
   *
   * @param OMDBRepositoryInterface $omdbRepository Репозиторий для взаимодействия с OMDB API.
   * @return void
   */
  public function handle(OMDBRepositoryInterface $omdbRepository): void
  {
    // todo - вынести в контроллер в соответствии с ТЗ
    if (Serial::where('imdb_id', $this->imdbId)->exists()) {
      Log::info("Сериал с IMDB ID {$this->imdbId} уже занесён в Базу данных.");
      return;
    }

    $data = $omdbRepository->getFilmInformation($this->imdbId);

    if ($data === null) {
      Log::warning("Сериал с IMDB ID {$this->imdbId} не найден или ошибка API.");
      //выбросить SerialNotFoundException
      return;
    }

    $serial = Serial::create([
      'imdb_id' => $data['imdbID'] ?? $this->imdbId,
      'title' => $data['Title'] ?? 'Unknown Title',
      'title_original' => $data['Title'] ?? 'Unknown Title',
      'year' => $data['Year'] ?? 'Unknown Year',
    ]);

    Log::info("Сохранённый сериал: {$serial->title} (IMDB: {$serial->imdb_id})");
  }

  /**
   * Вызывается при неудачном выполнении задачи.
   *
   * @param \Throwable $exception Исключение, вызвавшее сбой.
   * @return void
   */
  public function failed(\Throwable $exception): void
  {
    Log::error("Ошибка фоновой задачи для IMDB ID {$this->imdbId}: " . $exception->getMessage());
  }
}
