<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

/**
 * Модель сериала.
 *
 * @property int $id
 * @property string $imdb_id
 * @property string $title
 * @property string $title_original
 * @property \Illuminate\Support\Carbon $year
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read int $total_episodes
 * @property-read int $total_seasons
 * @property-read int $watched_episodes
 * @property-read string|null $watch_status
 * @property-read int|null $user_vote
 * @property-read float $rating
 */
class Serial extends Model
{
  use HasFactory;

  public const USER_WATCHING_STATUS = 'watching';

  /**
   * Имя таблицы в базе данных.
   *
   * @var string
   */
  protected $table = 'serials';

  /**
   * Список атрибутов, разрешённых для массового присвоения (например, через create() или update()).
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'imdb_id',
    'title',
    'title_original',
    'year',
  ];

  /**
   * Связи, которые всегда должны подгружаться вместе с моделью (eager load).
   *
   * @var array<int, string>
   */
  protected $with = ['genres'];

  /**
   * Агрегатные счётчики, автоматически добавляемые к модели.
   *
   * @var array<string, string>
   */
  protected $withCount = [
    'seasons as total_seasons',
    'episodes as total_episodes',
  ];

  /**
   * Список вычисляемых атрибутов, которые всегда добавляются при сериализации модели в JSON.
   *
   * @var array<int, string>
   */
  protected $appends = [
    'watched_episodes',
    'watch_status',
    'user_vote',
    'rating',
  ];

  /**
   * Список атрибутов, которые не должны попадать в JSON-ответ.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'created_at',
    'updated_at',
    'pivot',
  ];

  /**
   * Приведение типов для атрибутов при сериализации/десериализации.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'total_seasons' => 'int',
    'total_episodes' => 'int',
    'year' => 'date',
  ];

  /**
   * Связь "многие-ко-многим" с жанрами.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function genres()
  {
    return $this->belongsToMany(Genre::class, 'genre_serial');
  }

  /**
   * Связь "один-ко-многим" с сезонами.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function seasons()
  {
    return $this->hasMany(Season::class);
  }

  /**
   * Связь "один-ко-многим" с эпизодами.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function episodes()
  {
    return $this->hasMany(Episode::class);
  }

  /**
   * Связь "один-ко-многим" с голосами пользователей за сериал.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function votes()
  {
    return $this->hasMany(SerialVote::class);
  }

  /**
   * Все записи о том, что пользователи отметили сериал как "просматриваемый".
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function serialWatchingRecords()
  {
    return $this->hasMany(SerialWatching::class, 'serial_id');
  }

  /**
   * Вычисляемый атрибут: сколько эпизодов этого сериала просмотрел текущий пользователь.
   *
   * @return int
   */
  public function getWatchedEpisodesAttribute()
  {
    if (Auth::guest()) {
      return 0;
    }

    // Считаем, сколько эпизодов этого сериала пользователь отметил как просмотренные
    return Episode::where('serial_id', $this->id)
      ->whereHas('usersWatched', function ($query) {
        $query->where('user_id', Auth::id());
      })
      ->count();
  }

  /**
   * Вычисляемый атрибут: статус просмотра сериала текущим пользователем.
   *
   * @return string|null
   */
  public function getWatchStatusAttribute()
  {
    if (Auth::guest()) {
      return null;
    }

    $isWatched = $this->serialWatchingRecords()
      ->where('user_id', Auth::id())
      ->exists();

    return $isWatched ? self::USER_WATCHING_STATUS : null;
  }

  /**
   * Вычисляемый атрибут: голос текущего пользователя за сериал.
   *
   * @return int|null
   */
  public function getUserVoteAttribute()
  {
    if (Auth::guest()) {
      return null;
    }

    $vote = $this->votes()->where('user_id', Auth::id())->first();
    return $vote?->vote;
  }

  /**
   * Вычисляемый атрибут: средний рейтинг сериала по всем голосам.
   *
   * @return \Illuminate\Database\Eloquent\Casts\Attribute
   */
  protected function rating(): Attribute
  {
    return Attribute::get(function () {
      if ($this->votes()->count() === 0) {
        return 0.0;
      }

      return round((float) $this->votes()->avg('vote'), 1);
    });
  }
}
