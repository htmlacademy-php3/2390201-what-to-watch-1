<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\FailResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Throwable $e) {
            if ($e instanceof ValidationException) {
                return new FailResponse(
                  $e->errors(), // ошибки по полям из request
                  'Переданные данные не корректны.',
                  422
                );
            }

            if ($e instanceof AuthorizationException) {
                return new FailResponse([], $e->getMessage(), 403);
            }

            if ($e instanceof AuthenticationException) {
                return new FailResponse([], 'Запрос требует аутентификации.', 401);
            }

            return null;
        });
    })->create();
