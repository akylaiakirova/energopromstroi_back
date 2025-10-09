<?php

namespace App\Http\Controllers;

use App\Models\UserBank;
use Illuminate\Http\Request;

/**
 * CRUD для реквизитов сотрудников (user_banks).
 */
class UserBankController extends Controller
{
    /**
     * Получить список реквизитов. Можно фильтровать по user_id (?user_id=ID).
     */
    public function index(Request $request)
    {
        $query = UserBank::query();
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }
        return $query->orderBy('bank_name')->get();
    }

    /** Создать реквизиты. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:255'],
            'bank_bik' => ['nullable', 'string', 'max:255'],
            'address_registered' => ['nullable', 'string', 'max:255'],
            'address_actual' => ['nullable', 'string', 'max:255'],
        ]);

        $item = UserBank::create($data);
        return response()->json($item, 201);
    }

    /** Обновить реквизиты. */
    public function update(Request $request, UserBank $user_bank)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:255'],
            'bank_bik' => ['nullable', 'string', 'max:255'],
            'address_registered' => ['nullable', 'string', 'max:255'],
            'address_actual' => ['nullable', 'string', 'max:255'],
        ]);

        $user_bank->update($data);
        return response()->json($user_bank);
    }

    /** Удалить реквизиты. */
    public function destroy(UserBank $user_bank)
    {
        $user_bank->delete();
        return response()->json(['message' => 'Удалено']);
    }
}


