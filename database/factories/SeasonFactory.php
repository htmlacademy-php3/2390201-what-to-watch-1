<?php

namespace Database\Factories;

use App\Models\Serial;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeasonFactory extends Factory
{
  public function definition(): array
  {
    return [
      'serial_id' => Serial::factory(),
      'number' => $this->faker->numberBetween(1, 10),
      'title' => $this->faker->words(3, true),
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
