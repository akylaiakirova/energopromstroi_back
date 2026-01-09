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
        // Включаем CORS для запросов между поддоменами (и локальной отладки).
        // Настройки лежат в config/cors.php
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // Диагностика CORS именно для /api/auth/login (OPTIONS/POST) в storage/logs/laravel.log
        $middleware->append(\App\Http\Middleware\CorsAuthDebugMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
