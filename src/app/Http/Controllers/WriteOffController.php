<?php

namespace App\Http\Controllers;

use App\Models\WriteOff;

class WriteOffController extends Controller
{
    /** Список списаний с полной информацией о материале. */
    public function index()
    {
        return WriteOff::with('material')->orderByDesc('id')->get();
    }
}




