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
      'imdb_id' => 'tt' . $this->faker->unique()->numberBetween(1000000, 9999999),
      'title' => $this->faker->words(3, true),
      'title_original' => $this->faker->words(3, true),
      'year' => $this->faker->dateTimeBetween('-10 years', 'now'),
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
