<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Constants\UserRole;
use App\Models\Role;

/**
 * Модель пользователя
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $avatar
 * @property int $role_id
 */
class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * Имя таблицы в базе данных.
   *
   * @var string
   */
  protected $table = 'users';

  /**
   * Атрибуты, которые можно массово заполнять.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'avatar',
    'role_id',
  ];

  /**
   * Атрибуты, которые должны быть скрыты при сериализации.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Атрибуты, которые должны быть приведены к типам.
   *
    * @return array<string, string>
    */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  /**
   * Получить комментарии пользователя.
   */
  public function comments()
  {
    return $this->hasMany(Comment::class);
  }

  /**
   * Получить оценки сериалов пользователем
   */
  public function serialVotes()
  {
    return $this->hasMany(SerialVote::class);
  }

  /**
   * Получить сериалы, которые посмотрел пользователь.
   */
  public function watchingSerials()
  {
    return $this->belongsToMany(Serial::class, 'serial_watching');
  }

  /**
   * Получить эпизоды, которые посмотрел пользователь.
   */
  public function watchedEpisodes()
  {
    return $this->belongsToMany(Episode::class, 'episodes_watched');
  }

  /**
   * Получить роль пользователя.
   */
  public function role()
  {
    return $this->belongsTo(Role::class);
  }

  /**
   * Проверяет, является ли пользователь модератором.
   */
  public function isModerator(): bool
  {
    return $this->role?->name === UserRole::MODERATOR;
  }
}
