<?php

namespace App\Http\Controllers;

use App\Models\MaterialsConsumption;
use Illuminate\Http\Request;

/**
 * CRUD для таблицы materials_consumption — нормативный расход материалов по мощности котла.
 */
class MaterialsConsumptionController extends Controller
{
    /** Получить список норм расхода (связи подгружаем). Фильтры по boiler_capacity_id, material_id. */
    public function index(Request $request)
    {
        $query = MaterialsConsumption::with(['boilerCapacity', 'material'])->orderBy('id', 'desc');

        if ($request->filled('boiler_capacity_id')) {
            $query->where('boiler_capacity_id', (int) $request->get('boiler_capacity_id'));
        }
        if ($request->filled('material_id')) {
            $query->where('material_id', (int) $request->get('material_id'));
        }

        return $query->get();
    }

    /** Создать норму расхода. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'boiler_capacity_id' => ['required', 'integer', 'exists:boilers_capacity,id'],
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'countStandard' => ['required', 'integer', 'min:0'],
        ]);

        $item = MaterialsConsumption::create($data)->load(['boilerCapacity', 'material']);
        return response()->json($item, 201);
    }

    /** Обновить норму расхода. */
    public function update(Request $request, MaterialsConsumption $materials_consumption)
    {
        $data = $request->validate([
            'boiler_capacity_id' => ['required', 'integer', 'exists:boilers_capacity,id'],
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'countStandard' => ['required', 'integer', 'min:0'],
        ]);

        $materials_consumption->update($data);
        return response()->json($materials_consumption->load(['boilerCapacity', 'material']));
    }

    /** Удалить норму расхода. */
    public function destroy(MaterialsConsumption $materials_consumption)
    {
        $materials_consumption->delete();
        return response()->json(['message' => 'Удалено']);
    }
}

