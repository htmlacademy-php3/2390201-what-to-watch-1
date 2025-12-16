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

  // Получение списка просматриваемых сериалов пользователя
  public function watchlist(): BaseResponse
  {
    try {
      $data = []; // получаем список просматриваемых сериалов пользователя
      return new SuccessResponse($data);
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
        'data' => [
          'name' => $updatedUser->name,
          'email' => $updatedUser->email,
          'avatar' => $updatedUser->avatar,
        ],
      ]);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }
}
