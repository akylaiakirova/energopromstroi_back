<?php

namespace App\Http\Controllers;

use App\Models\TemplateDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * CRUD для шаблонов документов (templates_document).
 */
class TemplateDocumentController extends Controller
{
    /** Список шаблонов по name ASC. */
    public function index()
    {
        return TemplateDocument::orderBy('name')->get();
    }

    /** Создать шаблон. Автогенерация имён файлов: td_YYYYmmdd_HHMMSS_filename. */
    public function store(Request $request)
    {
        if ($request->hasFile('files')) {
            // multipart: validating uploaded files
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'files' => ['nullable', 'array'],
                'files.*' => ['file'],
                'note' => ['nullable', 'string'],
            ]);
            $data['files'] = $this->fileNamesFromUploads($request->file('files'));
        } else {
            // json: expecting array of file names (strings)
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'files' => ['nullable', 'array'],
                'files.*' => ['string'],
                'note' => ['nullable', 'string'],
            ]);
            // оставляем имена как есть, без переименования
            $data['files'] = $data['files'] ?? [];
        }

        $item = TemplateDocument::create($data);
        return response()->json($item, 201);
    }

    /** Обновить шаблон. */
    public function update(Request $request, TemplateDocument $templates_document)
    {
        if ($request->hasFile('files')) {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'files' => ['nullable', 'array'],
                'files.*' => ['file'],
                'note' => ['nullable', 'string'],
            ]);
            $data['files'] = $this->fileNamesFromUploads($request->file('files'));
        } else {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'files' => ['nullable', 'array'],
                'files.*' => ['string'],
                'note' => ['nullable', 'string'],
            ]);
            if (array_key_exists('files', $data)) {
                // если передали массив строк — сохраняем как есть
                $data['files'] = $data['files'] ?? [];
            }
        }

        $templates_document->update($data);
        return response()->json($templates_document);
    }

    /** Удалить шаблон. */
    public function destroy(TemplateDocument $templates_document)
    {
        $templates_document->delete();
        return response()->json(['message' => 'Удалено']);
    }

    /**
     * Сгенерировать имена из загруженных файлов: td_YYYYmmdd_HHMMSS_filename
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
            $name = 'td_'.$now.'_'.$safe;
            // store the uploaded file on the public disk (storage/app/public)
            // using the generated safe name so it can be served via /storage/{name}
            // save into 'td' subdirectory on the public disk
            $file->storeAs('td', $name, 'public');
            $names[] = $name;
        }
        return $names;
    }
}


