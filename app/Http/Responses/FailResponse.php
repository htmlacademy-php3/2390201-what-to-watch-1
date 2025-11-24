<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

/**
 * Класс для формирования неуспешного HTTP-ответа.
 */
class FailResponse extends BaseResponse
{
  public int $statusCode = Response::HTTP_BAD_REQUEST;

  /**
   * Конструктор неуспешного ответа.
   *
   * @param mixed $data Данные об ошибках
   * @param string|null $message Описание ошибки
   * @param int $code HTTP-код состояния (по умолчанию 400)
   */
  public function __construct($data, protected ?string $message = null, int $code = Response::HTTP_BAD_REQUEST)
  {
    parent::__construct([], $code);
  }

  /**
   * Формирует содержимое ответа для неуспешного запроса.
   *
   * @return array Структурированный массив с сообщением об ошибке и деталями ошибок
   */
  protected function makeResponseData(): array
  {
    return [
      'message' => $this->message,
      'errors' => $this->prepareData(),
    ];
  }
}
