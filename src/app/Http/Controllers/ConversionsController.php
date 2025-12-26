<?php

namespace App\Http\Controllers;

use App\Models\Conversion;
use App\Models\ConversionMaterial;
use App\Models\BoilerReady;
use App\Models\MaterialsConsumption;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Единый контроллер для работы с конвертацией и связанными материалами.
 */
class ConversionsController extends Controller
{
    /**
     * Сводная выборка:
     * - materials_consumption: все записи (короткая форма)
     * - conversions: список конвертаций с массивом conversion_materials (деталей)
     */
    public function index()
    {
        $materialsConsumption = MaterialsConsumption::query()
            ->orderBy('id')
            ->get(['boiler_capacity_id', 'material_id', 'countStandard', 'createAt', 'updatedAt']);

        $conversions = Conversion::with(['materials' => function ($q) {
            $q->orderBy('id');
        }])->orderBy('id', 'desc')->get();

        $conversionsPayload = $conversions->map(function (Conversion $c) {
            return [
                'id' => $c->id,
                'boiler_capacity_id' => $c->boiler_capacity_id,
                'responsible_user_id' => $c->responsible_user_id,
                'note' => $c->note,
                'finishAt' => $c->finishAt,
                'conversion_materials' => $c->materials->map(function (ConversionMaterial $m) {
                    return [
                        'id' => $m->id,
                        'conversions_id' => $m->conversions_id,
                        'material_id' => $m->material_id,
                        'countStandard' => $m->countStandard,
                        'countFact' => $m->countFact,
                        'createAt' => $m->createAt,
                        'updateAt' => $m->updateAt,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'materials_consumption' => $materialsConsumption,
            'conversions' => $conversionsPayload,
        ]);
    }

    /**
     * Создать конвертацию и связанные записи conversion_materials.
     * Тело запроса:
     * {
     *   "boiler_capacity_id": 1|null,
     *   "responsible_user_id": 1,
     *   "note": "строка",
     *   "conversion_materials": [
     *     { "material_id": 1, "countStandard": 100, "countFact": 100, "createAt": "...", "updateAt": "..." },
     *     ...
     *   ]
     * }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'boiler_capacity_id' => ['nullable', 'integer', 'exists:boilers_capacity,id'],
            'responsible_user_id' => ['required', 'integer', 'exists:users,id'],
            'note' => ['nullable', 'string'],
            'conversion_materials' => ['required', 'array', 'min:1'],
            'conversion_materials.*.material_id' => ['required', 'integer', 'exists:materials,id'],
            'conversion_materials.*.countStandard' => ['required', 'numeric', 'min:0'],
            'conversion_materials.*.countFact' => ['required', 'numeric', 'min:0'],
            'conversion_materials.*.createAt' => ['nullable', 'date'],
            'conversion_materials.*.updateAt' => ['nullable', 'date'],
        ]);

        return DB::transaction(function () use ($data) {
            $conversion = Conversion::create([
                'boiler_capacity_id' => $data['boiler_capacity_id'] ?? null,
                'responsible_user_id' => $data['responsible_user_id'],
                'note' => $data['note'] ?? null,
            ]);

            $materialsPayload = [];
            foreach ($data['conversion_materials'] as $row) {
                $materialsPayload[] = [
                    'conversions_id' => $conversion->id,
                    'material_id' => $row['material_id'],
                    'countStandard' => $row['countStandard'],
                    'countFact' => $row['countFact'],
                    'createAt' => $row['createAt'] ?? Carbon::now(),
                    'updateAt' => $row['updateAt'] ?? null,
                ];
            }
            ConversionMaterial::insert($materialsPayload);

            $conversion->load('materials');
            return response()->json([
                'id' => $conversion->id,
                'boiler_capacity_id' => $conversion->boiler_capacity_id,
                'responsible_user_id' => $conversion->responsible_user_id,
                'note' => $conversion->note,
                'finishAt' => $conversion->finishAt,
                'conversion_materials' => $conversion->materials->map(function (ConversionMaterial $m) {
                    return [
                        'id' => $m->id,
                        'conversions_id' => $m->conversions_id,
                        'material_id' => $m->material_id,
                        'countStandard' => $m->countStandard,
                        'countFact' => $m->countFact,
                        'createAt' => $m->createAt,
                        'updateAt' => $m->updateAt,
                    ];
                })->values(),
            ], 201);
        });
    }

    /**
     * Обновить одну запись conversion_materials по её id.
     */
    public function updateConversionMaterial(Request $request, int $id)
    {
        $item = ConversionMaterial::findOrFail($id);

        $data = $request->validate([
            'material_id' => ['sometimes', 'integer', 'exists:materials,id'],
            'countStandard' => ['sometimes', 'numeric', 'min:0'],
            'countFact' => ['sometimes', 'numeric', 'min:0'],
            'createAt' => ['sometimes', 'date'],
            'updateAt' => ['sometimes', 'date'],
        ]);

        if (!array_key_exists('updateAt', $data)) {
            $data['updateAt'] = Carbon::now();
        }

        $item->update($data);
        return response()->json($item);
    }

    /**
     * Обновить ответственного пользователя у конвертации.
     */
    public function updateResponsibleUser(Request $request, int $id)
    {
        $data = $request->validate([
            'responsible_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $conversion = Conversion::findOrFail($id);
        $conversion->update(['responsible_user_id' => $data['responsible_user_id']]);
        return response()->json($conversion);
    }

    /**
     * Завершить конвертацию — выставить текущее серверное время в finishAt.
     */
    public function finishConversion(Request $request, int $id)
    {
        $data = $request->validate([
            'boiler_capacity_id' => ['required', 'integer', 'exists:boilers_capacity,id'],
            'responsible_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        return DB::transaction(function () use ($id, $data) {
            $conversion = Conversion::findOrFail($id);

            if (!is_null($conversion->finishAt)) {
                return response()->json(['message' => 'Конвертация уже завершена'], 422);
            }

            $now = Carbon::now();

            $conversion->update([
                'boiler_capacity_id' => $data['boiler_capacity_id'],
                'responsible_user_id' => $data['responsible_user_id'],
                'finishAt' => $now,
            ]);

            BoilerReady::create([
                'boiler_capacity_id' => $data['boiler_capacity_id'],
                'count' => 1,
                'user_id' => $data['responsible_user_id'],
                'createAt' => $now,
            ]);

            return response()->json($conversion);
        });
    }

    /**
     * Удалить конвертацию и все связанные conversion_materials.
     * Связь в БД настроена на cascadeOnDelete, но удалим саму конвертацию — материалы удалятся каскадом.
     */
    public function deleteConversionsId(int $id)
    {
        $conversion = Conversion::findOrFail($id);
        $conversion->delete();
        return response()->json(['message' => 'Удалено']);
    }

    /**
     * Удалить одну запись из conversion_materials по id.
     */
    public function conversionMaterialId(int $id)
    {
        $item = ConversionMaterial::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Удалено']);
    }
}


