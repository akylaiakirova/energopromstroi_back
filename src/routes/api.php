<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoilerCapacityController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserBankController;
use App\Http\Controllers\TemplateDocumentController;
use App\Http\Controllers\TemplatePaymentController;
use App\Http\Controllers\LettersController;
use App\Http\Controllers\BoilerPassportsController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\MaterialsArrivalController;
use App\Http\Controllers\StockBalanceController;
use App\Http\Controllers\WriteOffController;
use App\Http\Controllers\CashTypeController;
use App\Http\Controllers\CashboxController;
use App\Http\Controllers\MaterialsConsumptionController;
use App\Http\Controllers\ConversionsController;
use App\Http\Controllers\BoilerReadyController;
use App\Models\StockBalance;

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

    // Шаблоны платежей (templates_payment)
    Route::get('templates-payment', [TemplatePaymentController::class, 'index']);
    Route::post('templates-payment', [TemplatePaymentController::class, 'store']);
    Route::put('templates-payment/{templates_payment}', [TemplatePaymentController::class, 'update']);
    Route::delete('templates-payment/{templates_payment}', [TemplatePaymentController::class, 'destroy']);

    // Письма (letters)
    Route::get('letters', [LettersController::class, 'index']);
    Route::post('letters', [LettersController::class, 'store']);
    Route::put('letters/{letter}', [LettersController::class, 'update']);
    Route::delete('letters/{letter}', [LettersController::class, 'destroy']);

    // Паспорта котлов (boiler_passports)
    Route::get('boiler-passports', [BoilerPassportsController::class, 'index']);
    Route::post('boiler-passports', [BoilerPassportsController::class, 'store']);
    Route::put('boiler-passports/{boiler_passport}', [BoilerPassportsController::class, 'update']);
    Route::delete('boiler-passports/{boiler_passport}', [BoilerPassportsController::class, 'destroy']);

    // Договоры (contracts)
    Route::get('contracts', [ContractController::class, 'index']);
    Route::post('contracts', [ContractController::class, 'store']);
    Route::put('contracts/{contract}', [ContractController::class, 'update']);
    Route::delete('contracts/{contract}', [ContractController::class, 'destroy']);

    // Поставщики (suppliers)
    Route::get('suppliers', [SupplierController::class, 'index']);
    Route::post('suppliers', [SupplierController::class, 'store']);
    Route::put('suppliers/{supplier}', [SupplierController::class, 'update']);
    Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy']);

    // Поступление материалов (materials_arrival)
    Route::get('materials-arrival', [MaterialsArrivalController::class, 'index']);
    Route::post('materials-arrival', [MaterialsArrivalController::class, 'store']);
    Route::put('materials-arrival/{materials_arrival}', [MaterialsArrivalController::class, 'update']);
    Route::delete('materials-arrival/{materials_arrival}', [MaterialsArrivalController::class, 'destroy']);

    // Нормативный расход материалов (materials_consumption)
    Route::get('materials-consumption', [MaterialsConsumptionController::class, 'index']);
    Route::post('materials-consumption', [MaterialsConsumptionController::class, 'store']);
    Route::put('materials-consumption/{materials_consumption}', [MaterialsConsumptionController::class, 'update']);
    Route::delete('materials-consumption/{materials_consumption}', [MaterialsConsumptionController::class, 'destroy']);

    // Конвертации и связанные материалы (conversions + conversion_materials)
    Route::get('conversions', [ConversionsController::class, 'index']);
    Route::post('conversions', [ConversionsController::class, 'store']);
    Route::put('conversions/materials/{id}', [ConversionsController::class, 'updateConversionMaterial']);
    Route::put('conversions/{id}/responsible-user', [ConversionsController::class, 'updateResponsibleUser']);
    Route::post('conversions/{id}/finish', [ConversionsController::class, 'finishConversion']);
    Route::delete('conversions/{id}', [ConversionsController::class, 'deleteConversionsId']);
    Route::delete('conversions/materials/{id}', [ConversionsController::class, 'conversionMaterialId']);
    
    // Готовые котлы (boilers_ready) — сводка
    Route::get('boilers-ready/summary', [BoilerReadyController::class, 'summary']);

    // Остаток материалов (stocks_balance) — только чтение
    Route::get('stocks-balance', [StockBalanceController::class, 'index']);
    // Списание материалов (write_off)
    Route::post('stocks-balance/write-off', [StockBalanceController::class, 'whiteOff']);
    Route::get('write-off', [WriteOffController::class, 'index']);

    // Типы доходов/расходов (cash_types)
    Route::get('cash-types', [CashTypeController::class, 'index']);

    // Касса (cashbox)
    Route::get('cashbox', [CashboxController::class, 'index']);
    Route::post('cashbox', [CashboxController::class, 'store']);
    Route::put('cashbox/{cashbox}', [CashboxController::class, 'update']);
    Route::delete('cashbox/{cashbox}', [CashboxController::class, 'destroy']);
});


