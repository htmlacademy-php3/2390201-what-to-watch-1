<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Тесты для AuthController: регистрация, вход, выход.
 */
class AuthControllerTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Успешная регистрация пользователя без аватара.
   */
  public function test_register_creates_user_and_returns_token(): void
  {
    $response = $this->postJson('/api/register', [
      'name' => 'Иван Иванов',
      'email' => 'iivanov@example.com',
      'password' => 'secret123',
      'password_confirmation' => 'secret123',
    ]);

    $response->assertStatus(201)
      ->assertJsonStructure(['data' => ['token', 'user']]);

    $this->assertDatabaseHas('users', [
      'email' => 'iivanov@example.com',
      'name' => 'Иван Иванов',
    ]);

    $this->assertTrue(Hash::check('secret123', User::first()->password));
  }

  /**
   * Проверка валидации при регистрации: все поля обязательны.
   * Ожидается ответ с message и errors в кастомном формате.
   */
  public function test_register_returns_validation_errors_in_custom_format(): void
  {
    $response = $this->postJson('/api/register', [
      'name' => '',
      'email' => '',
      'password' => '',
      'password_confirmation' => '',
    ]);

    $response->assertStatus(422)
      ->assertJson([
        'message' => 'Переданные данные не корректны.',
        'errors' => [
          'name' => ['Поле Имя обязательно для заполнения.'],
          'email' => ['Поле E-Mail адрес обязательно для заполнения.'],
          'password' => ['Поле Пароль обязательно для заполнения.'],
        ],
      ]);
  }

  /**
   * Проверка уникальности email при регистрации.
   */
  public function test_register_email_must_be_unique(): void
  {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->postJson('/api/register', [
      'name' => 'New User',
      'email' => 'existing@example.com',
      'password' => 'secret123',
      'password_confirmation' => 'secret123',
    ]);

    $response->assertStatus(422)
      ->assertJsonStructure(['message', 'errors'])
      ->assertJsonPath('errors.email.0', 'Пользователь с таким E-Mail уже существует.');
  }

  /**
   * Регистрация с корректным аватаром (менее 10 МБ).
   */
  public function test_register_with_valid_avatar(): void
  {
    Storage::fake('public');

    $avatar = UploadedFile::fake()->image('avatar.jpg', 100, 100)->size(2048);

    $response = $this->postJson('/api/register', [
      'name' => 'Проверка Аватара',
      'email' => 'avatar@example.com',
      'password' => 'password',
      'password_confirmation' => 'password',
      'file' => $avatar,
    ]);

    $response->assertStatus(201);

    $user = User::first();
    $this->assertNotNull($user->avatar, 'Аватар не был сохранён в БД');
  }
  
  /**
   * Регистрация отклоняется, если аватар больше 10 МБ.
   */
  public function test_register_rejects_avatar_larger_than_10mb(): void
  {
    Storage::fake('public');

    // Создаём файл размером 10241 КБ (>10 МБ)
    $largeFile = UploadedFile::fake()->image('big.jpg')->size(10241);

    $response = $this->postJson('/api/register', [
      'name' => 'Проверка размера аватара',
      'email' => 'big@example.com',
      'password' => 'secret123',
      'password_confirmation' => 'secret123',
      'file' => $largeFile,
    ]);

    $response->assertStatus(422)
      ->assertJsonStructure(['message', 'errors'])
      ->assertJsonPath('errors.file.0', 'Файл аватара не должен превышать 10 МБ.');
  }

  /**
   * Успешный вход с корректными учетными данными.
   */
  public function test_login_returns_token_on_valid_credentials(): void
  {
    $user = User::factory()->create([
      'email' => 'login@example.com',
      'password' => Hash::make('secret123'),
    ]);

    $response = $this->postJson('/api/login', [
      'email' => 'login@example.com',
      'password' => 'secret123',
    ]);

    $response->assertStatus(200)
      ->assertJsonStructure(['data' => ['token']]);
  }

  /**
   * Вход отклоняется при неверных учетных данных.
   * Проверяется кастомное сообщение об ошибке.
   */
  public function test_login_fails_with_invalid_credentials(): void
  {
    $response = $this->postJson('/api/login', [
      'email' => 'fake@example.com',
      'password' => 'wrong',
    ]);

    $response->assertStatus(422)
      ->assertJson([
        'message' => 'Неверное имя пользователя или пароль.',
        'errors' => [
          'exception' => ['Неверное имя пользователя или пароль.'],
        ],
      ]);
  }

  /**
   * Выход из системы удаляет текущий токен Sanctum.
   */
  public function test_logout_deletes_current_token(): void
  {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
      ->postJson('/api/logout');

    $response->assertStatus(204);

    $this->assertDatabaseMissing('personal_access_tokens', [
      'tokenable_id' => $user->id,
      'name' => 'test-token',
    ]);
  }
}
