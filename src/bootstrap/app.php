<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Включаем CORS для запросов между поддоменами (и локальной отладки).
        // Настройки лежат в config/cors.php
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // Диагностика CORS именно для /api/auth/login (OPTIONS/POST) в storage/logs/laravel.log
        $middleware->append(\App\Http\Middleware\CorsAuthDebugMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Логирование причин 401/Unauthenticated для API
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $reason = null;
            if ($e instanceof TokenExpiredException) {
                $reason = 'token_expired';
            } elseif ($e instanceof TokenInvalidException) {
                $reason = 'token_invalid';
            } elseif ($e instanceof TokenBlacklistedException) {
                $reason = 'token_blacklisted';
            } elseif ($e instanceof JWTException) {
                $reason = 'jwt_exception';
            } elseif ($e instanceof AuthenticationException) {
                $reason = 'unauthenticated';
            }

            if ($reason !== null) {
                $authHeader = $request->headers->get('Authorization', '');
                $tokenSnippet = null;
                if (str_starts_with($authHeader, 'Bearer ')) {
                    $token = substr($authHeader, 7);
                    if (strlen($token) > 12) {
                        $tokenSnippet = substr($token, 0, 6).'...'.substr($token, -6);
                    } else {
                        $tokenSnippet = $token;
                    }
                }

                Log::warning('API auth failure', [
                    'reason' => $reason,
                    'path' => $request->getPathInfo(),
                    'method' => $request->getMethod(),
                    'origin' => $request->headers->get('Origin'),
                    'has_authorization' => $request->headers->has('Authorization'),
                    'token_snippet' => $tokenSnippet,
                ]);
            }

            return null;
        });
    })->create();
