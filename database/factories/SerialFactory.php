<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Serial>
 */
class SerialFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'imdb_id' => fake()->unique()->lexify('tt???????'),
      'title' => fake()->sentence(3),
      'title_original' => fake()->sentence(3),
      'year' => now()->subYears(fake()->numberBetween(1, 30)),
    ];
  }
}
