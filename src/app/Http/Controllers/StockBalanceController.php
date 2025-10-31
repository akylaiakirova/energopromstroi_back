<?php

namespace App\Http\Controllers;

use App\Models\StockBalance;
use App\Models\WriteOff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockBalanceController extends Controller
{
    /**
     * Список остатков материалов с полной информацией о материале.
     */
    public function index()
    {
        $items = StockBalance::with('material')->orderBy('material_id')->get();
        return $items;
    }

    /**
     * Списание материала: создаёт запись в write_off и уменьшает остаток stocks_balance.
     */
    public function whiteOff(Request $request)
    {
        $data = $request->validate([
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'count' => ['required', 'integer', 'min:1'],
            'price_for_1' => ['nullable', 'numeric'],
            'note' => ['nullable', 'string'],
        ]);

        $result = DB::transaction(function () use ($data) {
            $balance = StockBalance::firstOrCreate(
                ['material_id' => $data['material_id']],
                ['count' => 0]
            );

            $current = (int) $balance->count;
            $deduct = (int) $data['count'];
            if ($deduct > $current) {
                abort(422, 'Недостаточный остаток материала для списания');
            }

            $balance->count = $current - $deduct;
            $balance->save();

            $writeOff = WriteOff::create([
                'material_id' => $data['material_id'],
                'count' => $data['count'],
                'price_for_1' => $data['price_for_1'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            return $writeOff;
        });

        return response()->json($result, 201);
    }
}
