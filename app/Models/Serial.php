<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Модель сериала
 *
 * @property int $id
 * @property string $imdb_id
 * @property string $title
 * @property string $title_original
 * @property \Illuminate\Support\Carbon $year
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $rating
 */
class Serial extends Model
{
  use HasFactory;
  
  /**
   * Имя таблицы в базе данных.
   *
   * @var string
   */
  protected $table = 'serials';

  /**
   * Атрибуты, которые можно массово заполнять.
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
   * Получить жанры сериала.
   */
  public function genres()
  {
    return $this->belongsToMany(Genre::class, 'genre_serial');
  }

  /**
   * Получить сезоны сериала.
   */
  public function seasons()
  {
    return $this->hasMany(Season::class);
  }

  /**
   * Получить эпизоды сериала.
   */
  public function episodes()
  {
    return $this->hasMany(Episode::class);
  }

  /**
   * Получить голоса за сериал.
   */
  public function votes()
  {
    return $this->hasMany(SerialVote::class);
  }

  /**
   * Получить пользователей, которые посмотрели этот сериал.
   */
  public function usersWatched()
  {
    return $this->belongsToMany(User::class, 'serials_watched');
  }

  /**
   * Получить эпизоды сериала, которые были просмотрены (через промежуточную таблицу).
   */
  public function usersWatchedEpisodes()
  {
    return $this->belongsToMany(Episode::class, 'episodes_watched');
  }

  /**
   * Вычисляет средний рейтинг на основе голосов (поле vote в serials_votes).
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
