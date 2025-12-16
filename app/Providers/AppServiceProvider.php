<?php

namespace App\Providers; 

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Comment;
use Romnosk\Repository\OMDBRepositoryInterface;
use Romnosk\Repository\OMDBRepository;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
      // Регистрация OMDB репозитория через контейнер
      $this->app->bind(OMDBRepositoryInterface::class, function () {
        return new OMDBRepository(
          Psr18ClientDiscovery::find(),
          Psr17FactoryDiscovery::findRequestFactory(),
          config('services.omdb.api_key')
        );
      });
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
