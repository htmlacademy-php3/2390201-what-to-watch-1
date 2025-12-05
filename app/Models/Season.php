<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Модель сезона
 *
 * @property int $id
 * @property int $serial_id
 * @property int $number
 * @property string|null $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Season extends Model
{
  use HasFactory;

  /**
   * Имя таблицы в базе данных.
   *
   * @var string
   */
  protected $table = 'seasons';

  /**
   * Атрибуты, которые можно массово заполнять.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'serial_id',
    'number',
    'title',
  ];

  /**
   * Получить сериал, к которому принадлежит сезон.
   */
  public function serial()
  {
    return $this->belongsTo(Serial::class);
  }

  /**
   * Получить эпизоды сезона.
   */
  public function episodes()
  {
    return $this->hasMany(Episode::class);
  }
}
