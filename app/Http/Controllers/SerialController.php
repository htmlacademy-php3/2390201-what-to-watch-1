<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Services\SerialService;
use App\Models\Serial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

// Контроллер для работы с сериалами
class SerialController extends Controller
{
  private const PER_PAGE = 20;

  private SerialService $serialService;

  public function __construct(SerialService $serialService)
  {
    $this->serialService = $serialService;
  }

  // Получение списка сериалов
  public function index(Request $request): BaseResponse
  {
    try {
      $validated = $request->validate([
        'page' => 'sometimes|integer|min:1',
        'order_by' => 'sometimes|in:date,rating',
        'order_to' => 'sometimes|in:asc,desc',
        'genre' => 'sometimes|string',
        'search' => 'sometimes|string',
      ]);

      $serials = $this->serialService->getSerialsList($validated, self::PER_PAGE);

      return new SuccessResponse($serials);
    } catch (Exception $e) {
      return new FailResponse([], $e->getMessage());
    }
  }

  // Получение информации о сериале
  public function show($id): BaseResponse
  {
    try {
      $serial = $this->serialService->getSerialDetails($id);
      return new SuccessResponse($serial);
    } catch (ModelNotFoundException $e) {
        return new FailResponse([], 'Serial not found', 404);
    } catch (Exception $e) {
        return new FailResponse([], $e->getMessage());
    }
  }

  // Добавление сериала в список просматриваемых текущим пользователем
  public function addToWatchlist($id): BaseResponse
    {
      try {
        $serial = Serial::findOrFail($id);
        $user = Auth::user();

        // Добавляем в список (через отношение из модели User)
        /** @var \App\Models\User $user */
        $user->watchingSerials()->syncWithoutDetaching($serial);

        return new SuccessResponse([]);
      } catch (Exception $e) {
        return new FailResponse([], $e->getMessage());
      }
    }

  // Удаление сериала из списка просматриваемых текущим пользователем
   public function removeFromWatchlist($id): BaseResponse
    {
      try {
        $serial = Serial::findOrFail($id);
        $user = Auth::user();

        /** @var \App\Models\User $user */
        $user->watchingSerials()->detach($serial);

        return new SuccessResponse([]);
      } catch (Exception $e) {
        return new FailResponse([], $e->getMessage());
      }
    }

  // Добавление оценки сериалу текущим пользователем
  public function vote($id, Request $request): BaseResponse
  {
    try {
      $validated = $request->validate([
          'vote' => 'required|integer|between:1,10',
      ]);

      $serial = Serial::findOrFail($id);
      $request->user()->serialVotes()->updateOrCreate(
          ['serial_id' => $serial->id],
          ['vote' => $validated['vote']]
      );

      return new SuccessResponse([]);
    } catch (ValidationException $e) {
        return new FailResponse($e->errors(), $e->getMessage(), 422);
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
