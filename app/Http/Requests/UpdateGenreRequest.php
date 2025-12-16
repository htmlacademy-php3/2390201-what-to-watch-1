<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGenreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   * Возвращает true, так как авторизация обрабатывается через Gate в контроллере.
   *
   * @return bool
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Получает правила проверки, применяемые к запросу.
   *
   * @return array<string, string>
   */
  public function rules(): array
  {
    return [
      'title' => 'required|string|max:255',
    ];
  }

  /**
   * Получает настраиваемые сообщения валидации
   *
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'title.required' => 'Поле Наименование обязательно для заполнения.',
    ];
  }
}
