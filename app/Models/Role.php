<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Модель роли пользователя
 *
 * @property int $id
 * @property string $name
 */

class Role extends Model
{
  use HasFactory;

  protected $fillable = ['name'];
}
