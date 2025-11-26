<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\Request;
use Exception;

// Контроллер для работы с сериалами
class SerialController extends Controller
{
  // Получение списка сериалов
  public function index(Request $request): BaseResponse
  {
    try {
      $data = []; // получаем список сериалов
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Получение информации о сериале
  public function show($id): BaseResponse
  {
    try {
      $data = []; // получаем информацию о сериале
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Добавление сериала в список просматриваемых текущим пользователем
  public function addToWatchlist($id): BaseResponse
  {
    try {
      $data = []; // добавляем сериал в список просматриваемых
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Удаление сериала из списка просматриваемых текущим пользователем
  public function removeFromWatchlist($id): BaseResponse
  {
    try {
      $data = []; // удаляем сериал из списка просматриваемых
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Добавление оценки сериалу текущим пользователем
  public function vote($id, Request $request): BaseResponse
  {
    try {
      $data = []; // добавляем оценку сериалу
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Запрос на добавление сериала на сайт
  public function request(Request $request): BaseResponse
  {
    try {
      $data = []; // делаем запрос о добавлении сериала на сайт
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }
}
