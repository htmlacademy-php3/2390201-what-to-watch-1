<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Genre>
 */
class GenreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
      return [
        'title' => $this->faker->unique()->word(),
        'imdb_id' => 'tt' . $this->faker->unique()->numberBetween(1000000, 9999999),
        'created_at' => now(),
        'updated_at' => now(),
      ];
    }
}
