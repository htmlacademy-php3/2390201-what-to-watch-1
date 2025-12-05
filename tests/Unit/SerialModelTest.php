<?php

namespace Tests\Unit;

use App\Models\Serial;
use App\Models\SerialVote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SerialModelTest extends TestCase
{
  use RefreshDatabase;

  public function test_it_calculates_rating_correctly(): void
  {
    $serial = Serial::factory()->create();

    SerialVote::factory()->create(['serial_id' => $serial->id, 'vote' => 8]);
    SerialVote::factory()->create(['serial_id' => $serial->id, 'vote' => 6]);
    SerialVote::factory()->create(['serial_id' => $serial->id, 'vote' => 10]);

    // (8 + 6 + 10) / 3 = 8
    $this->assertEquals(8.0, $serial->rating);
  }

  public function test_it_returns_zero_when_no_votes(): void
  {
    $serial = Serial::factory()->create();

    $this->assertEquals(0.0, $serial->rating);
  }
}
