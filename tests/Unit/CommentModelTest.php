<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentModelTest extends TestCase
{
  use RefreshDatabase;

  public function test_it_returns_author_name_when_user_exists(): void
  {
    $user = User::factory()->create(['name' => 'Иван Иванов']);
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    $this->assertEquals('Иван Иванов', $comment->author_name);
  }

  public function test_it_returns_anonymous_name_when_user_is_null(): void
  {
    $comment = Comment::factory()->create(['user_id' => null]);

    $this->assertEquals('Аноним', $comment->author_name);
  }
}
