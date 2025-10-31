<?php

namespace App\Http\Controllers;

use App\Models\MaterialsArrival;
use App\Models\StockBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CRUD для поступления материалов (materials_arrival).
 */
class MaterialsArrivalController extends Controller
{
    /** Список поступлений по id DESC. */
    public function index()
    {
        $items = MaterialsArrival::with(['material', 'supplier'])
            ->orderBy('id', 'desc')
            ->get();

        return $items;
    }

    /** Создать поступление. total_price можно прислать или вычислим как count * price_for_1. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'count' => ['required', 'integer', 'min:1'],
            'price_for_1' => ['required', 'numeric'],
            'total_price' => ['nullable', 'numeric'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
        ]);
        if (!isset($data['total_price'])) {
            $data['total_price'] = (float) $data['count'] * (float) $data['price_for_1'];
        }

        $item = DB::transaction(function () use ($data) {
            $created = MaterialsArrival::create($data);
            // Обновляем остаток материалов по material_id
            $balance = StockBalance::firstOrCreate(
                ['material_id' => $data['material_id']],
                ['count' => 0]
            );
            $balance->count = (int) $balance->count + (int) $data['count'];
            $balance->save();
            return $created;
        });

        return response()->json($item, 201);
    }

    /** Обновить поступление. */
    public function update(Request $request, MaterialsArrival $materials_arrival)
    {
        $data = $request->validate([
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'count' => ['required', 'integer', 'min:1'],
            'price_for_1' => ['required', 'numeric'],
            'total_price' => ['nullable', 'numeric'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
        ]);
        if (!isset($data['total_price'])) {
            $data['total_price'] = (float) $data['count'] * (float) $data['price_for_1'];
        }

        $materials_arrival->update($data);
        return response()->json($materials_arrival);
    }

    /** Удалить поступление. */
    public function destroy(MaterialsArrival $materials_arrival)
    {
        $result = DB::transaction(function () use ($materials_arrival) {
            // Уменьшаем остаток материалов
            $balance = StockBalance::firstOrCreate(
                ['material_id' => $materials_arrival->material_id],
                ['count' => 0]
            );

            $balance->count = (int) $balance->count - (int) $materials_arrival->count;
            if ($balance->count < 0) {
                // Не позволяем уйти в минус — откатываем транзакцию и возвращаем ошибку
                abort(422, 'Удаление поступления привело бы к отрицательному остатку материалов');
            }
            $balance->save();

            $materials_arrival->delete();

            return ['message' => 'Удалено'];
        });

        return response()->json($result);
    }
}




