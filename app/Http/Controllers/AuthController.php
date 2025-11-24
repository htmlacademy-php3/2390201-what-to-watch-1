<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponce;
use App\Http\Responses\FailResponce;
use App\Http\Responses\SuccessResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
   * @param \Illuminate\Http\Request $request Входящий HTTP-запрос
   * @return \App\Http\Responses\BaseResponce Ответ в формате JSON
   */
  public function register(Request $request): BaseResponce
  {
    try {
      $avatar = null;

      // Загрузка аватара, если файл передан и валиден
      if ($request->hasFile('file')) {
        $file = $request->file('file');
        if ($file->isValid() && $file->getSize() <= 10 * 1024 * 1024) {
          $avatar = $file->store('avatars', 'public');
        }
      }

      // Создание пользователя
      $user = User::create([
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'password' => Hash::make($request->input('password')),
        'avatar' => $avatar,
      ]);

      $token = $user->createToken('auth-token')->plainTextToken; // Генерация токена Sanctum

      return new SuccessResponse([
        'token' => $token,
        'user' => [
          'name' => $user->name,
        ],
      ], 201);
    } catch (\Exception $e) {
      return new FailResponce([], $e->getMessage());
    }
  }

  /**
   * Авторизация существующего пользователя.
   *
   * Принимает email и пароль. При успешной аутентификации возвращает токен.
   * При неверных данных возвращает ошибку с кодом 422 и сообщением
   * "Неверное имя пользователя или пароль."
   *
   * @param \Illuminate\Http\Request $request Входящий HTTP-запрос
   * @return \App\Http\Responses\BaseResponce Ответ в формате JSON
   */
  public function login(Request $request): BaseResponce
  {
    try {
      $credentials = [
        'email' => $request->input('email'),
        'password' => $request->input('password'),
      ];

      // Попытка аутентификации
      if (!Auth::attempt($credentials)) {
        return new FailResponce(
          ['exception' => ['Неверное имя пользователя или пароль.']],
          'Неверное имя пользователя или пароль.',
          422
        );
      }

      $user = Auth::user();
      $token = $user->createToken('auth-token')->plainTextToken;

      // Успешный ответ с кодом 200 OK
      return new SuccessResponse([
        'token' => $token,
      ]);
    } catch (\Exception $e) {
      return new FailResponce([], $e->getMessage());
    }
  }

  /**
   * Выход пользователя из системы.
   *
   * Удаляет текущий токен Sanctum пользователя.
   * Требует авторизации (middleware auth:sanctum).
   *
   * @param \Illuminate\Http\Request $request Входящий HTTP-запрос
   * @return \App\Http\Responses\BaseResponce Ответ с кодом 204 No Content
   */
  public function logout(Request $request): BaseResponce
  {
    try {
      $request->user()->currentAccessToken()->delete(); // Удаление текущего токена
      return new SuccessResponse([], 204);
    } catch (\Exception $e) {
      return new FailResponce([], $e->getMessage());
    }
  }
}
