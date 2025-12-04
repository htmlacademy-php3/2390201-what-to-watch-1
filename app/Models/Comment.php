<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Модель комментария
 *
 * @property int $id
 * @property int $episode_id
 * @property int $user_id
 * @property string $description
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $author_name
 */
class Comment extends Model
{
  use HasFactory;

  /**
   * Имя таблицы в базе данных.
   *
   * @var string
   */
  protected $table = 'comments';

  /**
   * Атрибуты, которые можно заполнять массово.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'episode_id',
    'user_id',
    'description',
    'parent_id',
  ];

  /**
   * Получить эпизод, к которому относится комментарий.
   */
  public function episode()
  {
    return $this->belongsTo(Episode::class);
  }

  /**
   * Получить пользователя, оставившего комментарий.
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Получить родительский комментарий (если это ответ).
   */
  public function parent()
  {
    return $this->belongsTo(Comment::class, 'parent_id');
  }

  /**
   * Получить дочерние комментарии (ответы на этот комментарий).
   */
  public function children()
  {
    return $this->hasMany(Comment::class, 'parent_id');
  }

  /**
   * Получить имя автора комментария или "Аноним", если пользователь не задан.
   *
   * @return string
   */
  public function getAuthorNameAttribute(): string
  {
    return $this->user?->name ?? 'Аноним';
  }
}
