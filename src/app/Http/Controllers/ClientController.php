<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

/**
 * CRUD для клиентов (clients).
 */
class ClientController extends Controller
{
    /** Получить всех клиентов, сортировка по name ASC. */
    public function index()
    {
        return Client::orderBy('name')->get();
    }

    /** Создать клиента. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'number' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'regex:/^\d+$/'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'bank' => ['nullable', 'string', 'max:255'],
            'bank_bik' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'address_legal' => ['nullable', 'string', 'max:255'],
            'address_fact' => ['nullable', 'string', 'max:255'],
        ]);

        $item = Client::create($data);
        return response()->json($item, 201);
    }

    /** Обновить клиента. */
    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'number' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255', 'regex:/^\d+$/'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'bank' => ['nullable', 'string', 'max:255'],
            'bank_bik' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'address_legal' => ['nullable', 'string', 'max:255'],
            'address_fact' => ['nullable', 'string', 'max:255'],
        ]);

        $client->update($data);
        return response()->json($client);
    }

    /** Удалить клиента. */
    public function destroy(Client $client)
    {
        $client->delete();
        return response()->json(['message' => 'Удалено']);
    }
}


