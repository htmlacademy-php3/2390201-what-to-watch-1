<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserController extends Controller
{
  public function __construct(
    private readonly \App\Services\UserService $userService
  ) {}

  /**
   * Получает список сериалов, добавленных пользователем в "просматриваемые".
   */
  public function watchlist(): BaseResponse
  {
    try {
      $user = Auth::user();
      // Получаем список сериалов через сервис
      $serials = $this->userService->getWatchlist($user);
      return new SuccessResponse($serials);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Обновление профиля пользователя
  public function updateProfile(UserRequest $request): BaseResponse
  {
    try
    {
      $user = Auth::user();
      $updatedUser = $this->userService->updateProfile($user, $request->validated());

      return new SuccessResponse([
        'name' => $updatedUser->name,
        'email' => $updatedUser->email,
        'avatar' => $updatedUser->avatar,
      ]);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }
}
