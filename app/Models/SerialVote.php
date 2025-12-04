<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Модель голоса за сериал
 *
 * @property int $id
 * @property int $serial_id
 * @property int $user_id
 * @property int $vote
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class SerialVote extends Model
{
  use HasFactory;
  
  /**
   * Имя таблицы в базе данных.
   *
   * @var string
   */
  protected $table = 'serials_votes';

  /**
   * Атрибуты, которые можно массово заполнять.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'serial_id',
    'user_id',
    'vote',
  ];

  /**
   * Получить сериал, которому была поставлена оценка.
   */
  public function serial()
  {
    return $this->belongsTo(Serial::class);
  }

  /**
   * Получить пользователя, который поставил оценку.
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
