<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Mockery;

class UserServiceTest extends TestCase
{
  use RefreshDatabase;

  protected UserService $userService;

  protected function setUp(): void
  {
    parent::setUp();
    $this->userService = new UserService();
  }

  #[Test]
  public function it_updates_user_profile_with_new_data_and_avatar(): void
  {
    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('delete')->with('avatars/old.jpg')->once();
    $mockDisk->shouldReceive('putFileAs')
      ->with('avatars', Mockery::type(File::class), Mockery::type('string'), Mockery::any())
      ->andReturn('avatars/new_mocked_avatar.jpg')
      ->once();

    Storage::shouldReceive('disk')->with('public')->andReturn($mockDisk);

    $user = User::factory()->make([
      'name' => 'old_name',
      'email' => 'old@example.com',
      'avatar' => 'avatars/old.jpg',
      'password' => 'dummy_password',
    ]);
    $user->exists = true;

    $file = UploadedFile::fake()->image('avatar.jpg');
    $data = [
      'name' => 'new_name',
      'email' => 'new@example.com',
      'password' => 'new_password_123',
      'file' => $file,
    ];

    $updatedUser = $this->userService->updateProfile($user, $data);

    $this->assertEquals('new_name', $updatedUser->name);
    $this->assertEquals('new@example.com', $updatedUser->email);
    $this->assertTrue(Hash::check('new_password_123', $updatedUser->password));
    $this->assertEquals('avatars/new_mocked_avatar.jpg', $updatedUser->avatar);
  }

  #[Test]
  public function it_updates_profile_without_avatar_if_file_not_provided(): void
  {
    Storage::fake('public');

    // Пользователь без аватара
    $user = User::factory()->create([
      'name' => 'original',
      'email' => 'original@example.com',
      'avatar' => null,
    ]);

    $data = [
      'name' => 'updated_name',
      'email' => 'updated@example.com',
      // 'file' отсутствует
    ];

    $updatedUser = $this->userService->updateProfile($user, $data);

    $this->assertEquals('updated_name', $updatedUser->name);
    $this->assertEquals('updated@example.com', $updatedUser->email);
    $this->assertNull($updatedUser->avatar);
  }

  #[Test]
  public function it_does_not_update_password_if_not_provided(): void
  {
    $user = User::factory()->create([
      'password' => 'original_password_123',
    ]);

    $originalHash = $user->password;

    $data = [
      'name' => 'same_name',
      'email' => 'same@example.com',
      // 'password' отсутствует
    ];

    $updatedUser = $this->userService->updateProfile($user, $data);

    $this->assertEquals($originalHash, $updatedUser->password);
    $this->assertTrue(Hash::check('original_password_123', $updatedUser->password));
  }
}
