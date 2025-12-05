<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Модель просмотра сериала пользователем
 *
 * @property int $id
 * @property int $user_id
 * @property int $serial_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class SerialWatching extends Model
{
    use HasFactory;

    /**
     * Имя таблицы в базе данных.
     *
     * @var string
     */
    protected $table = 'serial_watching';

    /**
     * Атрибуты, которые можно массово заполнять.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'serial_id',
    ];

    /**
     * Получить пользователя, который просматривает сериал.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить сериал, который был просматривается.
     */
    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }
}
