<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Модель жанра
 *
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Genre extends Model
{
  use HasFactory;

  /**
   * Имя таблицы в базе данных.
   *
   * @var string
   */
  protected $table = 'genres';

  /**
   * Атрибуты, которые можно массово заполнять.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'title',
  ];

  /**
   * Получить сериалы, связанные с этим жанром.
   */
  public function serials()
  {
    return $this->belongsToMany(Serial::class, 'genre_serial');
  }
}
