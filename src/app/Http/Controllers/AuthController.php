<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Контроллер аутентификации по JWT.
 *
 * В данном контроллере реализованы базовые методы:
 * - register: регистрация нового пользователя
 * - login: получение JWT-токена по email/паролю
 * - me: получение данных текущего пользователя по токену
 * - refresh: обновление (рефреш) токена
 * - logout: выход и инвалидирование токена
 *
 * ВАЖНО: Миграции сейчас не запускаем, структура таблиц будет предоставлена отдельно.
 */
class AuthController extends Controller
{
    // Регистрация отключена по требованиям проекта

    /**
     * Логин пользователя. Возвращает JWT-токен при корректных данных.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Неверные учетные данные'], 401);
        }

        // Проверяем, имеет ли пользователь доступ в систему
        $user = auth('api')->user();
        if (! $user || ! $user->has_access) {
            auth('api')->logout();
            return response()->json(['message' => 'Доступ для этого пользователя отключен'], 403);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Данные текущего пользователя. Требуется валидный JWT.
     */
    public function me(Request $request)
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Обновление (рефреш) токена. Требуется валидный JWT.
     */
    public function refresh()
    {
        return response()->json([
            'access_token' => auth('api')->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Выход пользователя. Инвалидирует текущий токен.
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Вы вышли из системы']);
    }

    /**
     * Отправить временный пароль на email пользователя.
     * В целях простоты генерируем временный пароль и отправляем письмом.
     */
    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $data['email'])->first();
        $temp = Str::random(10);
        $user->password = Hash::make($temp);
        $user->save();

        // Отправка простого письма (используются настройки mail из .env)
        Mail::raw("Ваш временный пароль: {$temp}", function ($message) use ($user) {
            $message->to($user->email)->subject('Временный пароль');
        });

        return response()->json(['message' => 'Временный пароль отправлен на email']);
    }

    /**
     * Смена пароля текущим пользователем по JWT.
     */
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', Password::min(8)],
        ]);

        /** @var User $user */
        $user = auth('api')->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Текущий пароль неверен'], 422);
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        return response()->json(['message' => 'Пароль успешно изменён']);
    }
}


