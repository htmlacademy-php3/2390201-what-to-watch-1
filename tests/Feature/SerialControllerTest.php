<?php

namespace Tests\Feature;

use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SerialControllerTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();

    // Запускаем сидер
    $this->seed();
  }

  /** @test */
  public function it_returns_list_of_serials_with_pagination(): void
  {
    $response = $this->getJson('/api/shows');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          '*' => [
            'id',
            'title',
            'title_original',
            'status',
            'year',
            'rating',
            'total_seasons',
            'total_episodes',
            'genres' => [
              '*' => ['id', 'title']
            ],
            'watch_status',
            'watched_episodes',
            'user_vote',
          ]
        ]
      ]);

    // Проверяем пагинацию (20 элементов на страницу)
    $this->assertCount(20, $response->json('data'));
  }

  #[Test]
  public function it_can_filter_serials_by_genre(): void
  {
    // Получаем сериал с жанром
    $serial = Serial::with('genres')->first();
    $genre = $serial->genres->first();

    $response = $this->getJson("/api/shows?genre={$genre->title}");

    $response->assertStatus(200);

    // Проверяем, что все возвращенные сериалы имеют указанный жанр
    $serials = $response->json('data');
    foreach ($serials as $item) {
      $hasGenre = collect($item['genres'])->contains('title', $genre->title);
      $this->assertTrue($hasGenre);
    }
  }

  #[Test]
  public function it_can_search_serials_by_title(): void
  {
    $serial = Serial::factory()->create([
      'title' => 'Breaking Bad' // гарантируем "чистый" заголовок
    ]);

    $searchTerm = trim(substr($serial->title, 0, 3));
    // Защита от пустой строки
    if ($searchTerm === '') {
      $searchTerm = 'a';
    }

    $response = $this->getJson('/api/shows?search=' . urlencode($searchTerm));

    $response->assertStatus(200);

    $serials = $response->json('data');
    foreach ($serials as $item) {
      $hasSearchTerm =
        stripos($item['title'], $searchTerm) !== false ||
        stripos($item['title_original'], $searchTerm) !== false;
      $this->assertTrue($hasSearchTerm);
    }
  }

  #[Test]
  public function it_can_sort_serials_by_date(): void
  {
    $response = $this->getJson('/api/shows?order_by=date&order_to=asc');

    $response->assertStatus(200);

    $serials = $response->json('data');

    // Проверяем сортировку по дате (возрастание)
    for ($i = 0; $i < count($serials) - 1; $i++) {
      $this->assertLessThanOrEqual(
        $serials[$i + 1]['year'],
        $serials[$i]['year']
      );
    }
  }

  #[Test]
  public function it_can_sort_serials_by_rating(): void
  {
    $response = $this->getJson('/api/shows?order_by=rating&order_to=desc');

    $response->assertStatus(200);

    $serials = $response->json('data');

    // Проверяем сортировку по рейтингу (убывание)
    for ($i = 0; $i < count($serials) - 1; $i++) {
      $this->assertGreaterThanOrEqual(
        $serials[$i + 1]['rating'],
        $serials[$i]['rating']
      );
    }
  }

  #[Test]
  public function it_returns_serial_details(): void
  {
    $serial = Serial::with('genres')->first();

    $response = $this->getJson("/api/shows/{$serial->id}");

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'title',
          'title_original',
          'status',
          'year',
          'rating',
          'total_seasons',
          'total_episodes',
          'genres' => [
            '*' => ['id', 'title']
          ],
          'watch_status',
          'watched_episodes',
          'user_vote',
        ]
      ])
      ->assertJson([
        'data' => [
          'id' => $serial->id,
          'title' => $serial->title,
          'title_original' => $serial->title_original,
        ]
      ]);
  }

  #[Test]
  public function it_returns_404_for_nonexistent_serial(): void
  {
    $nonExistentId = 9999;

    $response = $this->getJson("/api/shows/{$nonExistentId}");

    $response->assertStatus(404);
  }

  #[Test]
  public function it_returns_watch_status_for_authenticated_user(): void
  {
    $user = User::factory()->create();
    $serial = Serial::factory()->create();

    // Добавляем в список просмотра
    $user->watchingSerials()->attach($serial);
    // Голосуем
    $user->serialVotes()->create(['serial_id' => $serial->id, 'vote' => 8]);

    /** @var \App\Models\User $user */
    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/shows/{$serial->id}");

    $data = $response->json('data');
    $this->assertNotNull($data['watch_status']);   // не null
    $this->assertNotNull($data['user_vote']);      // не null
  }

  #[Test]
  public function it_returns_null_watch_status_for_guest(): void
  {
    $serial = Serial::first();

    $response = $this->getJson("/api/shows/{$serial->id}");

    $response->assertStatus(200);

    $data = $response->json('data');

    // Для гостя поля должны быть null
    $this->assertNull($data['watch_status']);
    $this->assertEquals(0, $data['watched_episodes']);
    $this->assertNull($data['user_vote']);
  }

  #[Test]
  public function authenticated_user_can_add_serial_to_watchlist(): void
  {
    $user = User::factory()->create();
    $serial = Serial::factory()->create();

    /** @var \App\Models\User $user */
    $response = $this->actingAs($user, 'sanctum')
      ->postJson("/api/user/shows/watch/{$serial->id}");

    $response->assertStatus(200);

    // Проверяем, что сериал добавлен в список просматриваемых
    $this->assertDatabaseHas('serial_watching', [
      'user_id' => $user->id,
      'serial_id' => $serial->id,
    ]);
  }

  #[Test]
  public function authenticated_user_can_remove_serial_from_watchlist(): void
  {
    $user = User::factory()->create();
    $serial = Serial::factory()->create();

    // Сначала добавляем сериал в список
    $serial->serialWatchingRecords()->create(['user_id' => $user->id]);

    /** @var \App\Models\User $user */
    $response = $this->actingAs($user, 'sanctum')
      ->deleteJson("/api/user/shows/watch/{$serial->id}");

    $response->assertStatus(200);

    // Проверяем, что сериал удален из списка просматриваемых
    $this->assertDatabaseMissing('serial_watching', [
      'user_id' => $user->id,
      'serial_id' => $serial->id,
    ]);
  }

  #[Test]
  public function authenticated_user_can_vote_for_serial(): void
  {
    $user = User::factory()->create();
    $serial = Serial::factory()->create();

    /** @var \App\Models\User $user */
    $response = $this->actingAs($user, 'sanctum')
      ->postJson("/api/user/shows/{$serial->id}/vote", [
        'vote' => 9,
      ]);

    $response->assertStatus(200);

    // Проверяем, что оценка сохранена
    $this->assertDatabaseHas('serials_votes', [
      'user_id' => $user->id,
      'serial_id' => $serial->id,
      'vote' => 9,
    ]);
  }

  #[Test]
  public function guest_cannot_add_serial_to_watchlist(): void
  {
    $serial = Serial::factory()->create();

    $response = $this->postJson("/api/user/shows/watch/{$serial->id}");

    $response->assertStatus(401);
  }

  #[Test]
  public function guest_cannot_vote_for_serial(): void
  {
    $serial = Serial::factory()->create();

    $response = $this->postJson("/api/user/shows/{$serial->id}/vote", [
      'vote' => 9,
    ]);

    $response->assertStatus(401);
  }

  #[Test]
  public function vote_must_be_valid_number(): void
  {
    $user = User::factory()->create();
    $serial = Serial::factory()->create();

    /** @var \App\Models\User $user */
    $response = $this->actingAs($user, 'sanctum')
      ->postJson("/api/user/shows/{$serial->id}/vote", [
        'vote' => 15, // Недопустимое значение
      ]);

    $response->assertStatus(422);
  }
  
  //Чтобы отработал этот тест, параметр QUEUE_CONNECTION=sync
  #[Test]
  public function user_can_add_serial_via_imdb_id(): void
  {
    $user = User::factory()->create();

    /** @var \App\Models\User $user */
    $response = $this->actingAs($user, 'sanctum')
      ->postJson('/api/shows', [
        'imdb' => 'tt0944947',
      ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('serials', [
      'imdb_id' => 'tt0944947',
    ]);
  }

  #[Test]
  public function test_dispatches_job_on_valid_request(): void
  {
    Queue::fake();

    $user = User::factory()->create();

    /** @var \App\Models\User $user */
    $response = $this->actingAs($user, 'sanctum')
      ->postJson('/api/shows', [
        'imdb' => 'tt0944947',
      ]);

    Queue::assertPushed(\App\Jobs\TakeAndStoreSerialFromOmdb::class, function ($job) {
      return $job->imdbId === 'tt0944947';
    });
  }
}
