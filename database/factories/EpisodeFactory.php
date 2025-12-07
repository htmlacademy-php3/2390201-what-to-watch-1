<?php

namespace Database\Factories;

use App\Models\Season;
use App\Models\Serial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Episode>
 */
class EpisodeFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'season_id' => Season::factory(),
      'number' => $this->faker->numberBetween(1, 20),
      'title' => $this->faker->words(3, true),
      'air_date' => $this->faker->dateTimeBetween('-5 years', '+1 year'), 
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
