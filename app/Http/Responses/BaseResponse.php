<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Абстрактный базовый класс для HTTP-ответов.
 */
abstract class BaseResponse implements Responsable
{
  /**
   * Конструктор базового ответа.
   *
   * @param mixed $data Данные для включения в ответ (по умолчанию пустой массив)
   * @param int $statusCode HTTP-код ответа (по умолчанию 200 OK)
   */
  public function __construct(
    protected mixed $data = [],
    public int $statusCode = Response::HTTP_OK,
  ) {
  }

  /**
   * Создает HTTP-ответ, представляющий объект.
   *
   * @param Request $request Запрос, для которого создается ответ
   * @return Response HTTP-ответ в формате JSON
   */
  public function toResponse($request)
  {
    return response()->json($this->makeResponseData(), $this->statusCode);
  }

  /**
   * Преобразует возвращаемые данные в массив.
   * Если данные реализуют интерфейс Arrayable, вызывает метод toArray().
   *
   * @return array Массив данных для ответа
   */
  protected function prepareData(): array
  {
    if ($this->data instanceof Arrayable) {
      return $this->data->toArray();
    }

    return $this->data;
  }

  /**
   * Абстрактный метод для формирования содержимого ответа.
   * Должен быть реализован в дочерних классах.
   *
   * @return array|null Массив данных для тела ответа
   */
  abstract protected function makeResponseData(): ?array;
}
