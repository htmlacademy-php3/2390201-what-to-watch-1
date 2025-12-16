<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
  /**
   * Обновляет профиль пользователя на основе валидированных данных.
   *
   * @param  User  $user
   * @param  array  $data
   * @return User
   */
  public function updateProfile(User $user, array $data): User
  {
    $updateData = [
      'name' => $data['name'] ?? $user->name,
      'email' => $data['email'] ?? $user->email,
    ];

    // Обработка пароля (если задан)
    if (!empty($data['password'])) {
      $updateData['password'] = Hash::make($data['password']);
    }

    // Обработка аватара (только если файл есть — не затираем существующий)
    if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
      // Удаляем старый аватар, если он есть
      if ($user->avatar) {
        Storage::disk('public')->delete($user->avatar);
      }
      $updateData['avatar'] = $data['file']->store('avatars', 'public');
    }

    $user->update($updateData);

    return $user;
  }
}
