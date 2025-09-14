<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| В этом файле определяются маршруты API. По умолчанию они подключаются
| с префиксом /api и оборачиваются в группу middleware 'api'.
| Здесь настроим endpoints для JWT-аутентификации без запуска миграций.
| Все комментарии — на русском языке для лучшей поддержки.
|
*/

Route::prefix('auth')->group(function () {
    // Вход пользователя и выдача JWT-токена
    Route::post('login', [AuthController::class, 'login']);

    // Запрос сброса пароля: отправка временного пароля на email
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

    // Смена пароля текущим пользователем (нужен валидный JWT)
    Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:api');

    // Обновление (рефреш) токена. Требует валидного JWT.
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');

    // Получение профиля текущего пользователя. Требует валидного JWT.
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');

    // Выход пользователя (инвалидирует текущий токен). Требует валидного JWT.
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
});


