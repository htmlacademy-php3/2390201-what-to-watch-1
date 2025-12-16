<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Constants\UserRole;

class GenreControllerTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Тест получения списка жанров.
   */
  public function test_index_returns_genres_list(): void
  {
    Genre::factory()->count(5)->create();

    $response = $this->getJson('/api/genres');

    $response
      ->assertOk()
      ->assertJsonStructure([
        'data' => [
          '*' => ['id', 'title'],
        ],
      ])
      ->assertJsonCount(5, 'data');
  }

  /**
   * Тест успешного обновления жанра модератором.
   */
  public function test_update_by_moderator_returns_updated_genre(): void
  {
    $genre = Genre::factory()->create(['title' => 'Старое наименование жанра']);


    $moderator = User::factory()->create(['role' => UserRole::MODERATOR]);
    Sanctum::actingAs($moderator, ['moderator']);

    $response = $this->patchJson("/api/genres/{$genre->id}", [
      'title' => 'Новое наименование жанра',
    ]);

    $response
      ->assertOk()
      ->assertJsonStructure([
        'data' => [
          '*' => ['id', 'title'],
        ],
      ])
      ->assertJson([
        'data' => [
          ['id' => $genre->id, 'title' => 'Новое наименование жанра'],
        ],
      ]);

    $this->assertDatabaseHas('genres', [
      'id' => $genre->id,
      'title' => 'Новое наименование жанра',
    ]);
  }

  /**
   * Тест обновления без аутентификации → 401.
   */
  public function test_update_without_auth_returns_401(): void
  {
    $genre = Genre::factory()->create();

    $response = $this->patchJson("/api/genres/{$genre->id}", [
      'title' => 'Новое наименование жанра',
    ]);

    $response
      ->assertUnauthorized()
      ->assertJson([
        'message' => 'Запрос требует аутентификации.',
      ]);
  }

  /**
   * Тест обновления обычным пользователем → 403.
   */
  public function test_update_by_regular_user_returns_403(): void
  {
    $genre = Genre::factory()->create();

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->patchJson("/api/genres/{$genre->id}", [
      'title' => 'Новое наименование жанра',
    ]);

    $response
      ->assertForbidden()
      ->assertJson([
        'message' => 'Неавторизованное действие.',
      ]);
  }

  /**
   * Тест обновления с невалидными данными → 422.
   */
  public function test_update_with_invalid_data_returns_422(): void
  {
    $genre = Genre::factory()->create();

    $moderator = User::factory()->create();
    Sanctum::actingAs($moderator, ['moderator']);

    // Передаём 'name' вместо 'title'
    $response = $this->patchJson("/api/genres/{$genre->id}", [
      'name' => 'Invalid Field',
    ]);

    $response
      ->assertUnprocessable()
      ->assertJson([
        'message' => 'Переданные данные не корректны.',
        'errors' => [
          'title' => ['Поле Наименование обязательно для заполнения.'],
        ],
      ]);
  }
}
