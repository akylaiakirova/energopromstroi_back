<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

/**
 * CRUD для таблицы materials (материалы).
 */
class MaterialController extends Controller
{
    /** Получить все материалы, сортировка по name ASC. */
    public function index()
    {
        return Material::orderBy('name')->get();
    }

    /** Создать материал. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:64'],
        ]);

        $item = Material::create($data);
        return response()->json($item, 201);
    }

    /** Обновить материал. */
    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:64'],
        ]);

        $material->update($data);
        return response()->json($material);
    }

    /** Удалить материал. */
    public function destroy(Material $material)
    {
        $material->delete();
        return response()->json(['message' => 'Удалено']);
    }
}


