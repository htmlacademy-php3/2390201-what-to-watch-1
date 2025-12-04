<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Модель эпизода
 *
 * @property int $id
 * @property string $title
 * @property int $serial_id
 * @property int $season_id
 * @property int $number
 * @property \Illuminate\Support\Carbon $air_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Episode extends Model
{
  use HasFactory;

  /**
   * Имя таблицы в базе данных.
   *
   * @var string
   */
  protected $table = 'episodes';

  /**
   * Атрибуты, которые можно массово заполнять.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'title',
    'serial_id',
    'season_id',
    'number',
    'air_date',
  ];

  /**
   * Получить сериал, к которому принадлежит эпизод.
   */
  public function serial()
  {
    return $this->belongsTo(Serial::class);
  }

  /**
   * Получить сезон, к которому принадлежит эпизод.
   */
  public function season()
  {
    return $this->belongsTo(Season::class);
  }

  /**
   * Получить комментарии к эпизоду.
   */
  public function comments()
  {
    return $this->hasMany(Comment::class);
  }

  /**
   * Получить пользователей, которые посмотрели этот эпизод.
   */
  public function usersWatched()
  {
    return $this->belongsToMany(User::class, 'episodes_watched');
  }
}
