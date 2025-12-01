<?php
// app/Constants/UserRole.php

namespace App\Constants;

/**
 * Класс, описывающий роли пользователей нашего API
 */
class UserRole
{
  const MODERATOR = 'moderator';
  const USER = 'user';

  /**
   * Возвращает все допустимые значения ролей.
   */
  public static function all(): array
  {
    return [
      self::MODERATOR,
      self::USER,
    ];
  }

  /**
   * Проверяет, является ли переданная строка допустимой ролью.
   */
  public static function isValid(string $role): bool
  {
    return in_array($role, self::all(), true);
  }
}
