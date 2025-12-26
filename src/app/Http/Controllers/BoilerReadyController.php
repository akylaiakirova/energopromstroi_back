<?php

namespace App\Http\Controllers;

use App\Models\BoilerReady;
use Illuminate\Support\Facades\DB;

class BoilerReadyController extends Controller
{
    /**
     * Сводка готовых котлов:
     * группировка по котлу (boiler_capacity_id), внутри по пользователю (user_id), sum(count).
     *
     * Формат ответа:
     * [
     *   { "boilerName": "...", "details": [ { "author": "...", "count": 1 }, ... ] },
     *   ...
     * ]
     */
    public function summary()
    {
        $rows = BoilerReady::query()
            ->join('boilers_capacity as bc', 'bc.id', '=', 'boilers_ready.boiler_capacity_id')
            ->join('users as u', 'u.id', '=', 'boilers_ready.user_id')
            ->whereNotNull('boilers_ready.boiler_capacity_id')
            ->whereNotNull('boilers_ready.user_id')
            ->select([
                'boilers_ready.boiler_capacity_id as boiler_capacity_id',
                'bc.name as boiler_name',
                'boilers_ready.user_id as user_id',
                'u.name as user_name',
                'u.surname as user_surname',
                DB::raw('SUM(boilers_ready.count) as total_count'),
            ])
            ->groupBy('boilers_ready.boiler_capacity_id', 'bc.name', 'boilers_ready.user_id', 'u.name', 'u.surname')
            ->orderBy('bc.name')
            ->orderBy('u.name')
            ->get();

        $grouped = [];
        foreach ($rows as $r) {
            $key = (int) $r->boiler_capacity_id;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'boilerName' => $r->boiler_name,
                    'details' => [],
                ];
            }

            $author = trim(($r->user_name ?? '').' '.($r->user_surname ?? ''));
            if ($author === '') {
                $author = $r->user_name ?? ('user#'.$r->user_id);
            }

            $grouped[$key]['details'][] = [
                'author' => $author,
                'count' => (int) $r->total_count,
            ];
        }

        return array_values($grouped);
    }
}


