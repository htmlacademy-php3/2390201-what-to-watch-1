<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\Request;
use Exception;

class EpisodeController extends Controller
{
  // Получение списка эпизодов сериала
  public function index($showId): BaseResponse
  {
    try {
      $data = []; // получаем список эпизодов сериала
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Получение информации об эпизоде $id
  public function show($id): BaseResponse
  {
    try {
      $data = []; // получаем информацию об эпизоде
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Добавление эпизода $id в список просмотренных
  public function markAsWatched($id): BaseResponse
  {
    try {
      $data = []; // добавляем эпизод в список просмотренных
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Удаление эпизода $id из списка просмотренных
  public function removeFromWatched($id): BaseResponse
  {
    try {
      $data = []; // удаляем эпизод из списка просмотренных
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Получение списка не просмотренных эпизодов для сериала $showId
  public function getUnwatchedEpisodes($showId): BaseResponse
  {
    try {
      $data = []; // получаем список не просмотренных эпизодов
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }
}
