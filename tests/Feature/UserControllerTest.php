<?php

namespace Tests\Feature;

use App\Models\Episode;
use App\Models\Season;
use App\Models\Serial;
use App\Models\SerialVote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Проверяет, что пользователь может получить список своих сериалов.
   */
  public function test_user_can_get_watchlist(): void
  {
    // Создаём и аутентифицируем пользователя
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    // Создаём сериал и добавляем его в "просматриваемые"
    $serial = Serial::factory()->create([
      'title' => 'Breaking Bad',
      'title_original' => 'Breaking Bad',
      'status' => 'ended',
      'year' => 2008,
    ]);

    $user->watchingSerials()->attach($serial->id);

    //Создаём сезон
    Season::factory()->create(['serial_id' => $serial->id]);

    // Создаём эпизоды
    Episode::factory()->count(3)->create(['serial_id' => $serial->id]);

    // Помечаем 2 эпизода как просмотренные
    $watchedEpisodes = Episode::where('serial_id', $serial->id)->take(2)->get();
    foreach ($watchedEpisodes as $episode) {
      $user->watchedEpisodes()->attach($episode->id);
    }

    // Добавляем оценку
    SerialVote::create([
      'user_id' => $user->id,
      'serial_id' => $serial->id,
      'vote' => 9,
    ]);

    // Запрашиваем список
    $response = $this->getJson('/api/user/shows');

    $response->assertStatus(200)
      ->assertJson([
        'data' => [
          [
            'id' => $serial->id,
            'title' => 'Breaking Bad',
            'title_original' => 'Breaking Bad',
            'status' => 'ended',
            'year' => 2008,
            'total_seasons' => 1,
            'total_episodes' => 3,
            'watched_episodes' => 2,
            'watch_status' => 'watching',
            'user_vote' => 9,
          ],
        ],
      ]);
  }
}
