<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\Request;
use Exception;

class UserController extends Controller
{
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
  public function updateProfile(Request $request): BaseResponse
  {
    try {
      $data = []; // обновляем профиль пользователя
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }
}
