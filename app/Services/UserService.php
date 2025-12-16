<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class UserService
{
    /**
   * Получает список сериалов, отмеченных пользователем как "просматриваемые".
   * Использует eager loading и вычисляемые атрибуты модели Serial.
   */
  public function getWatchlist(User $user): Collection
  {
    // Загружаем сериалы с необходимыми связями и счётчиками
    return $user->watchingSerials()
      ->withCount(['episodes as total_episodes'])
      ->get()
      ->map(function ($serial) {
        // Атрибуты watch_status, watched_episodes, user_vote вычисляются в модели Serial
        // на основе текущего Auth::user(), который совпадает с переданным $user
        return [
          'id' => $serial->id,
          'title' => $serial->title,
          'title_original' => $serial->title_original,
          'status' => $serial->status,
          'year' => $serial->year,
          'rating' => $serial->rating,
          'total_seasons' => $serial->total_seasons,
          'total_episodes' => $serial->total_episodes ?? 0,
          'watch_status' => $serial->watch_status,
          'watched_episodes' => $serial->watched_episodes ?? 0,
          'user_vote' => $serial->user_vote ?? 0,
        ];
      })
      ->values();
  }

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
