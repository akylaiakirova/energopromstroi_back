<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

/**
 * CRUD для поставщиков (suppliers).
 */
class SupplierController extends Controller
{
    /** Список поставщиков по name ASC. */
    public function index()
    {
        return Supplier::orderBy('name')->get();
    }

    /** Создать поставщика. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'regex:/^\d+$/'],
            'whatsapp' => ['nullable', 'string'],
            'telegram' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $item = Supplier::create($data);
        return response()->json($item, 201);
    }

    /** Обновить поставщика. */
    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'regex:/^\d+$/'],
            'whatsapp' => ['nullable', 'string'],
            'telegram' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $supplier->update($data);
        return response()->json($supplier);
    }

    /** Удалить поставщика. */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->json(['message' => 'Удалено']);
    }
}




