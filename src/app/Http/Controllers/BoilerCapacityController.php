<?php

namespace App\Http\Controllers;

use App\Models\BoilerCapacity;
use Illuminate\Http\Request;

/**
 * CRUD для таблицы boilers_capacity (мощности котлов).
 * Все методы снабжены базовой валидацией. Доступ — под JWT (middleware в маршрутах).
 */
class BoilerCapacityController extends Controller
{
    /**
     * Получить список мощностей, отсортированный по названию (name ASC).
     */
    public function index()
    {
        // Сортировка по числовому значению в поле name ("100" > "75")
        return BoilerCapacity::orderByRaw('(name + 0) ASC')->get();
    }

    /**
     * Создать новую мощность котла.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $item = BoilerCapacity::create($data);
        return response()->json($item, 201);
    }

    /**
     * Обновить мощность котла.
     */
    public function update(Request $request, BoilerCapacity $boilers_capacity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $boilers_capacity->update($data);
        return response()->json($boilers_capacity);
    }

    /**
     * Удалить мощность котла.
     */
    public function destroy(BoilerCapacity $boilers_capacity)
    {
        $boilers_capacity->delete();
        return response()->json(['message' => 'Удалено']);
    }
}


