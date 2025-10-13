<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;

/**
 * CRUD для договоров (contracts).
 */
class ContractController extends Controller
{
    /** Список договоров по дате DESC. */
    public function index()
    {
        return Contract::orderBy('date', 'desc')->get();
    }

    /** Создать договор. Автогенерация имён файлов: contracts_YYYYmmdd_HHMMSS_filename. */
    public function store(Request $request)
    {
        if ($request->hasFile('files')) {
            // multipart: validating uploaded files
            $data = $request->validate([
                'number' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'date' => ['required', 'date'],
                'files' => ['nullable', 'array'],
                'files.*' => ['file'],
                'note' => ['nullable', 'string'],
            ]);
            $data['files'] = $this->fileNamesFromUploads($request->file('files'));
        } else {
            // json: expecting array of file names (strings)
            $data = $request->validate([
                'number' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'date' => ['required', 'date'],
                'files' => ['nullable', 'array'],
                'files.*' => ['string'],
                'note' => ['nullable', 'string'],
            ]);
            // оставляем имена как есть, без переименования
            $data['files'] = $data['files'] ?? [];
        }

        $item = Contract::create($data);
        return response()->json($item, 201);
    }

    /** Обновить договор. */
    public function update(Request $request, Contract $contract)
    {
        if ($request->hasFile('files')) {
            $data = $request->validate([
                'number' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'date' => ['required', 'date'],
                'files' => ['nullable', 'array'],
                'files.*' => ['file'],
                'note' => ['nullable', 'string'],
            ]);
            $data['files'] = $this->fileNamesFromUploads($request->file('files'));
        } else {
            $data = $request->validate([
                'number' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
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

        $contract->update($data);
        return response()->json($contract);
    }

    /** Удалить договор. */
    public function destroy(Contract $contract)
    {
        $contract->delete();
        return response()->json(['message' => 'Удалено']);
    }

    /**
     * Сгенерировать имена из загруженных файлов: contracts_YYYYmmdd_HHMMSS_filename
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
            $name = 'contracts_'.$now.'_'.$safe;
            // store the uploaded file on the public disk (storage/app/public)
            // using the generated safe name so it can be served via /storage/contracts/{name}
            // save into 'contracts' subdirectory on the public disk
            $file->storeAs('contracts', $name, 'public');
            $names[] = $name;
        }
        return $names;
    }
}




