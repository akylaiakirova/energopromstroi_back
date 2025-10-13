<?php

namespace App\Http\Controllers;

use App\Models\BoilerPassport;
use Illuminate\Http\Request;

/**
 * CRUD для паспортов котлов (boiler_passports).
 */
class BoilerPassportsController extends Controller
{
    /** Список паспортов по дате DESC. */
    public function index()
    {
        return BoilerPassport::orderBy('date', 'desc')->get();
    }

    /** Создать паспорт. Автогенерация имён файлов: bp_YYYYmmdd_HHMMSS_filename. */
    public function store(Request $request)
    {
        if ($request->hasFile('files')) {
            // multipart: validating uploaded files
            $data = $request->validate([
                'boiler_capacity_id' => ['required', 'integer', 'exists:boilers_capacity,id'],
                'number' => ['required', 'string', 'max:255'],
                'date' => ['required', 'date'],
                'files' => ['nullable', 'array'],
                'files.*' => ['file'],
                'note' => ['nullable', 'string'],
            ]);
            $data['files'] = $this->fileNamesFromUploads($request->file('files'));
        } else {
            // json: expecting array of file names (strings)
            $data = $request->validate([
                'boiler_capacity_id' => ['required', 'integer', 'exists:boilers_capacity,id'],
                'number' => ['required', 'string', 'max:255'],
                'date' => ['required', 'date'],
                'files' => ['nullable', 'array'],
                'files.*' => ['string'],
                'note' => ['nullable', 'string'],
            ]);
            // оставляем имена как есть, без переименования
            $data['files'] = $data['files'] ?? [];
        }

        $item = BoilerPassport::create($data);
        return response()->json($item, 201);
    }

    /** Обновить паспорт. */
    public function update(Request $request, BoilerPassport $boiler_passport)
    {
        if ($request->hasFile('files')) {
            $data = $request->validate([
                'boiler_capacity_id' => ['required', 'integer', 'exists:boilers_capacity,id'],
                'number' => ['required', 'string', 'max:255'],
                'date' => ['required', 'date'],
                'files' => ['nullable', 'array'],
                'files.*' => ['file'],
                'note' => ['nullable', 'string'],
            ]);
            $data['files'] = $this->fileNamesFromUploads($request->file('files'));
        } else {
            $data = $request->validate([
                'boiler_capacity_id' => ['required', 'integer', 'exists:boilers_capacity,id'],
                'number' => ['required', 'string', 'max:255'],
                'date' => ['required', 'date'],
                'files' => ['nullable', 'array'],
                'files.*' => ['string'],
                'note' => ['nullable', 'string'],
            ]);
            if (array_key_exists('files', $data)) {
                // если передали массив строк — сохраняем как есть
                $data['files'] = $data['files'] ?? [];
            }
        }

        $boiler_passport->update($data);
        return response()->json($boiler_passport);
    }

    /** Удалить паспорт. */
    public function destroy(BoilerPassport $boiler_passport)
    {
        $boiler_passport->delete();
        return response()->json(['message' => 'Удалено']);
    }

    /**
     * Сгенерировать имена из загруженных файлов: bp_YYYYmmdd_HHMMSS_filename
     * Имя файла санитизируем (латинские буквы, цифры, ._-), остальное заменяем на _
     *
     * @param array<int,\Illuminate\Http\UploadedFile>|null $uploads
     * @return array<int,string>
     */
    private function fileNamesFromUploads(?array $uploads): array
    {
        if (! $uploads) {
            return [];
        }
        $now = now()->format('Ymd_His');
        $names = [];
        foreach ($uploads as $file) {
            $orig = $file->getClientOriginalName();
            $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
            $name = 'bp_'.$now.'_'.$safe;
            // store the uploaded file on the public disk (storage/app/public)
            // using the generated safe name so it can be served via /storage/bp/{name}
            // save into 'bp' subdirectory on the public disk
            $file->storeAs('bp', $name, 'public');
            $names[] = $name;
        }
        return $names;
    }
}




