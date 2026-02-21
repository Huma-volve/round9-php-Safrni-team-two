<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Auth\AuthenticationException;
use App\Helpers\ApiErrorCode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResponseHelper
{
    use \App\Traits\ApiResponse;
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // 401
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return (new ResponseHelper())->error(ApiErrorCode::UNAUTHORIZED, 'Unauthenticated.', null, 401);
            }
            return null;
        });

        // 403
        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return (new ResponseHelper())->error(ApiErrorCode::FORBIDDEN, 'Forbidden.', null, 403);
            }
            return null;
        });

        // 404 (ModelNotFoundException)
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return (new ResponseHelper())->error(ApiErrorCode::NOT_FOUND, 'Resource not found.', null, 404);
            }
            return null;
        });

        // 404 (Route)
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return (new ResponseHelper())->error(ApiErrorCode::NOT_FOUND, 'Endpoint not found.', null, 404);
            }
            return null;
        });

        // 405
        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return (new ResponseHelper())->error('METHOD_NOT_ALLOWED', 'Method not allowed.', null, 405);
            }
            return null;
        });

        // 422
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return (new ResponseHelper())->error(ApiErrorCode::VALIDATION, 'Validation error.', $e->errors(), 422);
            }
            return null;
        });

    })->create();