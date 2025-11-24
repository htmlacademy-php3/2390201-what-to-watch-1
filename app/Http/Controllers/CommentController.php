<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\Request;
use Exception;

class CommentController extends Controller
{
  // Получение списка комментариев эпизода $episodeId
  public function index($episodeId): BaseResponse
  {
    try {
      $data = []; // получаем список комментариев
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Добавление комментария к эпизоду $episodeId
  public function store($episodeId, Request $request): BaseResponse
  {
    try {
      $data = []; // добавляем комментарий к эпизоду
      return new SuccessResponse($data, 201);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Удаление комментария $commentId
  public function destroy($commentId): BaseResponse
  {
    try {
      $data = []; // удаляем комментарий
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }
}
