<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;

/**
 * CRUD для писем (letters).
 */
class LettersController extends Controller
{
    /** Список писем по theme ASC. */
    public function index()
    {
        return Letter::orderBy('theme')->get();
    }

    /** Создать письмо. Автогенерация имён файлов: letters_YYYYmmdd_HHMMSS_filename. */
    public function store(Request $request)
    {
        if ($request->hasFile('files')) {
            // multipart: validating uploaded files
            $data = $request->validate([
                'address' => ['required', 'string', 'max:255'],
                'theme' => ['required', 'string', 'max:255'],
                'text' => ['required', 'string'],
                'files' => ['nullable', 'array'],
                'files.*' => ['file'],
                'note' => ['nullable', 'string'],
            ]);
            $data['files'] = $this->fileNamesFromUploads($request->file('files'));
        } else {
            // json: expecting array of file names (strings)
            $data = $request->validate([
                'address' => ['required', 'string', 'max:255'],
                'theme' => ['required', 'string', 'max:255'],
                'text' => ['required', 'string'],
                'files' => ['nullable', 'array'],
                'files.*' => ['string'],
                'note' => ['nullable', 'string'],
            ]);
            // оставляем имена как есть, без переименования
            $data['files'] = $data['files'] ?? [];
        }

        $item = Letter::create($data);
        return response()->json($item, 201);
    }

    /** Обновить письмо. */
    public function update(Request $request, Letter $letter)
    {
        if ($request->hasFile('files')) {
            $data = $request->validate([
                'address' => ['required', 'string', 'max:255'],
                'theme' => ['required', 'string', 'max:255'],
                'text' => ['required', 'string'],
                'files' => ['nullable', 'array'],
                'files.*' => ['file'],
                'note' => ['nullable', 'string'],
            ]);
            $data['files'] = $this->fileNamesFromUploads($request->file('files'));
        } else {
            $data = $request->validate([
                'address' => ['required', 'string', 'max:255'],
                'theme' => ['required', 'string', 'max:255'],
                'text' => ['required', 'string'],
                'files' => ['nullable', 'array'],
                'files.*' => ['string'],
                'note' => ['nullable', 'string'],
            ]);
            if (array_key_exists('files', $data)) {
                // если передали массив строк — сохраняем как есть
                $data['files'] = $data['files'] ?? [];
            }
        }

        $letter->update($data);
        return response()->json($letter);
    }

    /** Удалить письмо. */
    public function destroy(Letter $letter)
    {
        $letter->delete();
        return response()->json(['message' => 'Удалено']);
    }

    /**
     * Сгенерировать имена из загруженных файлов: letters_YYYYmmdd_HHMMSS_filename
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
            $name = 'letters_'.$now.'_'.$safe;
            // store the uploaded file on the public disk (storage/app/public)
            // using the generated safe name so it can be served via /storage/letters/{name}
            // save into 'letters' subdirectory on the public disk
            $file->storeAs('letters', $name, 'public');
            $names[] = $name;
        }
        return $names;
    }
}




