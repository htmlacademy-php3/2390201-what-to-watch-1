<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use App\Http\Requests\UserRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Контроллер для обработки запросов аутентификации и регистрации пользователей.
 */
class AuthController extends Controller
{
  /**
   * Регистрация нового пользователя.
   *
   * Принимает имя, email, пароль, подтверждение пароля и опциональный файл аватара.
   * После сохранения в БД создаёт токен Sanctum и возвращает его клиенту.
   *
   * @param \App\Http\Requests\UserRequest $request Входящий HTTP-запрос
   * @return \App\Http\Responses\BaseResponse Ответ в формате JSON
   */
  public function register(UserRequest $request): BaseResponse
  {
    $avatar = null;

    // Загрузка аватара, если файл передан
    if ($request->hasFile('file')) {
      $avatar = $request->file('file')->store('avatars', 'public');
    }

    // Создание пользователя
    $user = User::create([
      'name' => $request->input('name'),
      'email' => $request->input('email'),
      'password' => Hash::make($request->input('password')),
      'avatar' => $avatar,
      'role' => UserRole::USER,
    ]);

    $token = $user->createToken('auth-token')->plainTextToken;

    return new SuccessResponse([
      'token' => $token,
      'user' => [
        'name' => $user->name,
      ],
    ], 201);
  }

  /**
   * Авторизация существующего пользователя.
   *
   * Принимает email и пароль. При успешной аутентификации возвращает токен.
   * При неверных данных возвращает ошибку с кодом 422 и сообщением
   * "Неверное имя пользователя или пароль."
   *
   * @param \Illuminate\Http\Request $request Входящий HTTP-запрос
   * @return \App\Http\Responses\BaseResponse Ответ в формате JSON
   */
  public function login(Request $request): BaseResponse
  {
    $email = $request->input('email');
    $password = $request->input('password');

    // Поиск пользователя по email
    $user = User::where('email', $email)->first();

    // Проверка существования пользователя и корректности пароля
    if (!$user || !Hash::check($password, $user->password)) {
      return new FailResponse(
        ['exception' => ['Неверное имя пользователя или пароль.']],
        'Неверное имя пользователя или пароль.',
        422
      );
    }

    $token = $user->createToken('auth-token')->plainTextToken;

    return new SuccessResponse([
      'token' => $token,
    ]);
  }

  /**
   * Выход пользователя из системы.
   *
   * Удаляет текущий токен Sanctum пользователя.
   * Требует авторизации (middleware auth:sanctum).
   *
   * @param \Illuminate\Http\Request $request Входящий HTTP-запрос
   * @return \App\Http\Responses\BaseResponse Ответ с кодом 204 No Content
   */
  public function logout(Request $request): BaseResponse
  {
    $request->user()->currentAccessToken()->delete(); // Удаление текущего токена
    return new SuccessResponse([], 204);
  }
}
