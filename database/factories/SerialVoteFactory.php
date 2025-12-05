<?php

namespace Database\Factories;

use App\Models\Serial;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SerialVote>
 */
class SerialVoteFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'serial_id' => Serial::factory(),
      'user_id' => User::factory(),
      'vote' => $this->faker->numberBetween(1, 5),
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
