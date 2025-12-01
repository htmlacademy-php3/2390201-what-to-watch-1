<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель роли пользователя
 *
 * @property int $id
 * @property string $name
 */

class Role extends Model
{
  protected $fillable = ['name'];
}
