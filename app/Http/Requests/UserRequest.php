<?php

namespace App\Http\Requests;

use App\Constants\Constants;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Форм-реквест для валидации данных при регистрации или обновлении профиля пользователя.
 * Автоматически применяет валидацию перед передачей управления в контроллер.
 */
class UserRequest extends FormRequest
{
  /**
   * Определяет, авторизован ли пользователь на выполнение этого запроса.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Возвращает правила валидации для полей запроса.
   *
   * @return array Массив правил валидации в формате Laravel
   */
  public function rules()
  {
    return [
      'name' => 'required|string|max:255',
      'email' => [
        'required',
        'string',
        'email',
        'max:255',
        $this->getUniqRule(),
      ],
      'password' => [
        $this->getPasswordRequiredRule(),
        'string',
        'min:8',
        'confirmed',
      ],

      'file' => [
        'nullable',
        'image',
        'max:' . (Constants::AVATAR_SIZE_KB),
      ],
    ];
  }

  /**
   * Возвращает требуемые сообщения при ошибках 422
   *
   * @return array Массив сообщений в формате Laravel
   */
  public function messages(): array
  {
    return [
      'name.required' => 'Поле Имя обязательно для заполнения.',
      'email.required' => 'Поле E-Mail адрес обязательно для заполнения.',
      'password.required' => 'Поле Пароль обязательно для заполнения.',
    ];
  }

  /**
   * Формирует правило уникальности для email.
   *
   * @return \Illuminate\Validation\Rules\Unique Правило уникальности
   */
  private function getUniqRule()
  {
    $rule = Rule::unique(User::class);

    // При PATCH-запросе (редактировании профиля) игнорирует текущего авторизованного пользователя
    if ($this->isMethod('patch') && Auth::check()) {
      return $rule->ignore(Auth::user());
    }

    return $rule;
  }

  /**
   * Определяет, является ли поле 'password' обязательным.
   *
   * - При регистрации (POST): пароль обязателен.
   * - При обновлении профиля (PATCH): пароль опционален
   *   (передаётся только если пользователь хочет его сменить).
   *
   * @return string 'required' или 'sometimes'
   */
  private function getPasswordRequiredRule()
  {
    return $this->isMethod('patch') ? 'sometimes' : 'required';
  }
}
