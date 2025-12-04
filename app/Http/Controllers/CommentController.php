<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
  public function destroy(Comment $comment): BaseResponse
  {
    try {
      if (!Gate::allows('comment-delete', $comment)) {
        return new FailResponse([], 'Недостаточно прав для удаления комментария.', 403);
      }

      $comment->delete();

      return new SuccessResponse(null, 201);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage(), 500);
    }
  }
}
