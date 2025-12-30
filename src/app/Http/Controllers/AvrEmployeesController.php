<?php

namespace App\Http\Controllers;

use App\Models\AvrEmployee;
use Illuminate\Http\Request;

/**
 * CRUD для АВР сотрудников (avr_employees).
 */
class AvrEmployeesController extends Controller
{
    /** Список АВР сотрудников по дате убыванию. */
    public function index(Request $request)
    {
        $query = AvrEmployee::with('user')->orderBy('date', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->get('user_id'));
        }

        return $query->get();
    }

    /** Создать запись АВР сотрудника. */
    public function store(Request $request)
    {
        $rules = [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'date' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ];

        if ($request->hasFile('files')) {
            $rules['files'] = ['nullable', 'array'];
            $rules['files.*'] = ['file'];
            $data = $request->validate($rules);
            $data['files'] = $this->fileNamesFromUploads($request->file('files'));
        } else {
            $rules['files'] = ['nullable', 'array'];
            $rules['files.*'] = ['string'];
            $data = $request->validate($rules);
            $data['files'] = $data['files'] ?? [];
        }

        $item = AvrEmployee::create($data);
        return response()->json($item->load('user'), 201);
    }

    /** Обновить запись АВР сотрудника. */
    public function update(Request $request, AvrEmployee $avr_employee)
    {
        $rules = [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'date' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ];

        if ($request->hasFile('files')) {
            $rules['files'] = ['nullable', 'array'];
            $rules['files.*'] = ['file'];
            $data = $request->validate($rules);
            $data['files'] = $this->fileNamesFromUploads($request->file('files'));
        } else {
            $rules['files'] = ['nullable', 'array'];
            $rules['files.*'] = ['string'];
            $data = $request->validate($rules);
            if (array_key_exists('files', $data)) {
                $data['files'] = $data['files'] ?? [];
            }
        }

        $avr_employee->update($data);
        return response()->json($avr_employee->load('user'));
    }

    /** Удалить запись АВР сотрудника. */
    public function destroy(AvrEmployee $avr_employee)
    {
        $avr_employee->delete();
        return response()->json(['message' => 'Удалено']);
    }

    /**
     * Сгенерировать имена из загруженных файлов: avr_emp_YYYYmmdd_HHMMSS_filename
     * Сохранение в папку avr_employees
     *
     * @param array<int,\Illuminate\Http\UploadedFile>|null $uploads
     * @return array<int,string>
     */
    private function fileNamesFromUploads(?array $uploads): array
    {
        if (!$uploads) {
            return [];
        }
        $now = now()->format('Ymd_His');
        $names = [];
        foreach ($uploads as $file) {
            $orig = $file->getClientOriginalName();
            $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
            $name = 'avr_emp_' . $now . '_' . $safe;
            $file->storeAs('avr_employees', $name, 'public');
            $names[] = $name;
        }
        return $names;
    }
}


