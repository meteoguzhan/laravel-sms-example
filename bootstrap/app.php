<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                $payload = new \App\Http\Resources\ErrorResource([
                    'code' => 'validation_error',
                    'message' => 'Validation failed',
                    'details' => $e->errors(),
                ]);
                return new \Illuminate\Http\JsonResponse($payload->toArray($request), 422);
            }
        });
        $exceptions->render(function (\App\Exceptions\ApiException $e, \Illuminate\Http\Request $request) {
            $payload = new \App\Http\Resources\ErrorResource([
                'code' => 'api_error',
                'message' => $e->getMessage(),
                'details' => $e->details,
            ]);
            return new \Illuminate\Http\JsonResponse($payload->toArray($request), $e->statusCode);
        });

        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                $payload = new \App\Http\Resources\ErrorResource([
                    'code' => 'server_error',
                    'message' => config('app.debug') ? $e->getMessage() : 'Internal Server Error',
                ]);
                return new \Illuminate\Http\JsonResponse($payload->toArray($request), 500);
            }
        });
    })->create();
