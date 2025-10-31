<?php

namespace App\Http\Controllers;

use App\Models\CashType;
use Illuminate\Http\Request;

class CashTypeController extends Controller
{
    /** Список типов (доходы/расходы), отсортирован по isIncome DESC, затем name ASC. */
    public function index(Request $request)
    {
        $query = CashType::query();

        if ($request->has('isIncome')) {
            $value = filter_var($request->query('isIncome'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($value !== null) {
                $query->where('isIncome', $value);
            }
        }

        return $query->orderByDesc('isIncome')->orderBy('name')->get();
    }
}




