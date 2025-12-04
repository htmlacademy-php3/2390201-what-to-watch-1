<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Модель просмотра эпизода пользователем
 *
 * @property int $id
 * @property int $user_id
 * @property int $serial_id
 * @property int $episode_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class EpisodeWatched extends Model
{
  use HasFactory;

  /**
   * Имя таблицы в базе данных.
   *
   * @var string
   */
  protected $table = 'episodes_watched';

  /**
   * Атрибуты, которые можно массово заполнять.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'serial_id',
    'episode_id',
  ];

  /**
   * Получить пользователя, который посмотрел эпизод.
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Получить сериал, к которому принадлежит эпизод.
   */
  public function serial()
  {
    return $this->belongsTo(Serial::class);
  }

  /**
   * Получить эпизод, который был просмотрен.
   */
  public function episode()
  {
    return $this->belongsTo(Episode::class);
  }
}
