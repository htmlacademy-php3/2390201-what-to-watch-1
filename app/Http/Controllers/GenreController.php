<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\Request;
use Exception;

class GenreController extends Controller
{
  // Получение списка жанров
  public function index(): BaseResponse
  {
    try {
      $data = []; // получаем список жанров
      return new SuccessResponse($data);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }
}
