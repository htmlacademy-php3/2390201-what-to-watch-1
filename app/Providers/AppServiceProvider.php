<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Comment;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
      // Обновлять жанры может только модератор
      Gate::define('update-genre', function (User $user) {
        return $user->isModerator();
      });

      // Удалять комментарий может модератор, либо автор комментария
      Gate::define('comment-delete', function (User $user, Comment $comment) {
        if ($user->isModerator()) {
          return true;
        }
        return $user->id === $comment->user_id;
      });
    }
}
