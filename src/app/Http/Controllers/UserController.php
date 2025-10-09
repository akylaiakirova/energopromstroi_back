<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * CRUD для сотрудников (users).
 */
class UserController extends Controller
{
    /** Получить список сотрудников, сортировка по name ASC. */
    public function index()
    {
        return User::orderBy('name')->get();
    }

    /** Создать сотрудника. Пароль обязателен при создании. Телефон — только цифры, email — валидный. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'position' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:255', 'regex:/^\d+$/'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'passport_pin' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric'],
            'comment' => ['nullable', 'string'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date'],
            'has_access' => ['nullable', 'boolean'],
            'password' => [Password::min(8)],
        ]);

        // role_id по умолчанию = 3, если не пришёл
        if (! array_key_exists('role_id', $data) || is_null($data['role_id'])) {
            $data['role_id'] = 3;
        }

        // Если у сотрудника есть доступ, пароль обязателен
        if ($request->boolean('has_access') && empty($data['password'])) {
            return response()->json(['message' => 'Пароль обязателен для сотрудников с доступом (has_access=true)'], 422);
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = User::create($data);
        return response()->json($user, 201);
    }

    /** Обновить сотрудника. Пароль — опционален; если передан, будет обновлён. */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'position' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:255', 'regex:/^\d+$/'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'passport_pin' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric'],
            'comment' => ['nullable', 'string'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date'],
            'has_access' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', Password::min(8)],
        ]);

        // role_id по умолчанию = 3, если не пришёл
        if (! array_key_exists('role_id', $data) || is_null($data['role_id'])) {
            $data['role_id'] = 3;
        }

        // Если became has_access=true и пароль пуст — ошибку
        if ($request->boolean('has_access') && empty($data['password']) && empty($user->password)) {
            return response()->json(['message' => 'Пароль обязателен для сотрудников с доступом (has_access=true)'], 422);
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return response()->json($user);
    }

    /** Удалить сотрудника. */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Удалено']);
    }
}


