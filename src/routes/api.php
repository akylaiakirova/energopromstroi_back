<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoilerCapacityController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserBankController;
use App\Http\Controllers\TemplateDocumentController;

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

// Мощности котлов (boilers_capacity) — CRUD (требует JWT)
Route::middleware('auth:api')->group(function () {
    Route::get('boilers-capacity', [BoilerCapacityController::class, 'index']);
    Route::post('boilers-capacity', [BoilerCapacityController::class, 'store']);
    Route::put('boilers-capacity/{boilers_capacity}', [BoilerCapacityController::class, 'update']);
    Route::delete('boilers-capacity/{boilers_capacity}', [BoilerCapacityController::class, 'destroy']);

    // Материалы
    Route::get('materials', [MaterialController::class, 'index']);
    Route::post('materials', [MaterialController::class, 'store']);
    Route::put('materials/{material}', [MaterialController::class, 'update']);
    Route::delete('materials/{material}', [MaterialController::class, 'destroy']);

    // Клиенты
    Route::get('clients', [ClientController::class, 'index']);
    Route::post('clients', [ClientController::class, 'store']);
    Route::put('clients/{client}', [ClientController::class, 'update']);
    Route::delete('clients/{client}', [ClientController::class, 'destroy']);

    // Сотрудники (users)
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);

    // Реквизиты сотрудников (user_banks)
    Route::get('user-banks', [UserBankController::class, 'index']);
    Route::post('user-banks', [UserBankController::class, 'store']);
    Route::put('user-banks/{user_bank}', [UserBankController::class, 'update']);
    Route::delete('user-banks/{user_bank}', [UserBankController::class, 'destroy']);

    // Шаблоны документов (templates_document)
    Route::get('templates-document', [TemplateDocumentController::class, 'index']);
    Route::post('templates-document', [TemplateDocumentController::class, 'store']);
    Route::put('templates-document/{templates_document}', [TemplateDocumentController::class, 'update']);
    Route::delete('templates-document/{templates_document}', [TemplateDocumentController::class, 'destroy']);
});


