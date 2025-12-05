<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    // Создаем тестового пользователя
    $user = User::factory()->create([
      'email' => 'test@example.com',
      'password' => bcrypt('password'),
    ]);

    // Создаем жанры
    $genres = Genre::factory()->count(10)->create();

    // Создаем сериалы и связываем их с жанрами
    $serials = Serial::factory()->count(30)->create();

    foreach ($serials as $serial) {
      // Каждому сериалу добавляем случайные жанры
      $serial->genres()->attach(
        $genres->random(rand(1, 3))->pluck('id')->toArray()
      );

      // Для первых 5 сериалов создаем оценки и статусы просмотра
      if ($serial->id <= 5) {
        // Создаем оценку от пользователя
        $serial->votes()->create([
          'user_id' => $user->id,
          'vote' => rand(6, 10),
        ]);

        // Добавляем в просматриваемые
        $serial->serialWatchingRecords()->create([
          'user_id' => $user->id,
        ]);

        // Создаем сезоны и эпизоды
        $seasons = $serial->seasons()->createMany(
          \App\Models\Season::factory()->count(rand(1, 3))->make()->toArray()
        );

        foreach ($seasons as $season) {
          $episodes = $season->episodes()->createMany(
            \App\Models\Episode::factory()
              ->count(rand(5, 10))
              ->make(['serial_id' => $serial->id])
              ->toArray()
          );
          // Отмечаем некоторые эпизоды как просмотренные
          $episodes->random(rand(2, 5))->each(function ($episode) use ($user) {
              $episode->usersWatched()->attach($user);
          });
        }
      }
    }

    // Создаем еще несколько оценок для вычисления среднего рейтинга
    $otherUsers = User::factory()->count(5)->create();

    foreach ($serials->take(10) as $serial) {
      foreach ($otherUsers as $otherUser) {
        if (rand(0, 1)) {
          $serial->votes()->create([
            'user_id' => $otherUser->id,
            'vote' => rand(1, 10),
          ]);
        }
      }
    }
  }
}
