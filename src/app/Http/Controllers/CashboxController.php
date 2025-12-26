<?php

namespace App\Http\Controllers;

use App\Models\Cashbox;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * CRUD для кассы (cashbox).
 */
class CashboxController extends Controller
{
    /** Список операций по дате убыванию. */
    public function index()
    {
        return Cashbox::with('cashType')->orderBy('dateTime', 'desc')->get();
    }

    /** Создать запись кассы. */
    public function store(Request $request)
    {
        // Support three input forms:
        // 1) multipart with a file part named 'payload' containing JSON
        // 2) multipart with binary file parts 'files' (or 'files[]') and regular form fields
        // 3) application/json body with fields

        $data = null;

        // 1) multipart payload file containing JSON
        if ($request->hasFile('payload')) {
            $payloadContent = $request->file('payload')->get();
            $payload = json_decode($payloadContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'payload is not valid JSON', 'details' => json_last_error_msg()], 400);
            }

            $v = Validator::make($payload, [
                'isIncome' => ['required', 'boolean'],
                'cash_types_id' => ['required', 'integer', 'exists:cash_types,id'],
                'sum' => ['required', 'numeric'],
                'note' => ['nullable', 'string'],
                'files' => ['nullable', 'array'],
                'files.*' => ['string'],
                'dateTime' => ['required', 'date'],
            ]);
            if ($v->fails()) {
                return response()->json(['message' => 'Validation failed', 'errors' => $v->errors()], 422);
            }
            $data = $v->validated();

            // handle uploaded binary files (if any) in addition to names in payload
            $uploadedFiles = $request->file('files') ?? $request->file('files[]') ?? [];
            $savedNames = [];
            if (!empty($uploadedFiles)) {
                // basic limits
                $maxFiles = 10;
                $maxSize = 50 * 1024 * 1024; // 50MB
                $allowedExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
                if (count($uploadedFiles) > $maxFiles) {
                    return response()->json(['error' => 'Too many files', 'max' => $maxFiles], 422);
                }
                foreach ($uploadedFiles as $f) {
                    if (! $f->isValid()) {
                        return response()->json(['error' => 'One of uploaded files is invalid'], 422);
                    }
                    if ($f->getSize() > $maxSize) {
                        return response()->json(['error' => 'File too large', 'file' => $f->getClientOriginalName()], 422);
                    }
                    $ext = strtolower($f->getClientOriginalExtension());
                    if (!in_array($ext, $allowedExt)) {
                        return response()->json(['error' => 'File type not allowed', 'file' => $f->getClientOriginalName()], 422);
                    }
                    $saved = $this->fileNamesFromUploads([$f]);
                    $savedNames = array_merge($savedNames, $saved);
                }
            }
            $data['files'] = array_merge($data['files'] ?? [], $savedNames);

        // 2) multipart with direct form fields and files[] binary parts
        } elseif ($request->hasFile('files') || $request->hasFile('files[]')) {
            // validate form fields (they are regular fields in the multipart request)
            $data = $request->validate([
                'isIncome' => ['required', 'boolean'],
                'cash_types_id' => ['required', 'integer', 'exists:cash_types,id'],
                'sum' => ['required', 'numeric'],
                'note' => ['nullable', 'string'],
                'files' => ['nullable', 'array'],
                'files.*' => ['file'],
                'dateTime' => ['required', 'date'],
            ]);
            $data['files'] = $this->fileNamesFromUploads($request->file('files') ?? $request->file('files[]'));

        // 3) JSON body
        } else {
            // if request is JSON, parse and validate
            if ($request->isJson() || str_contains($request->header('content-type', ''), 'application/json')) {
                $payload = $request->json()->all();
                $v = Validator::make($payload, [
                    'isIncome' => ['required', 'boolean'],
                    'cash_types_id' => ['required', 'integer', 'exists:cash_types,id'],
                    'sum' => ['required', 'numeric'],
                    'note' => ['nullable', 'string'],
                    'files' => ['nullable', 'array'],
                    'files.*' => ['string'],
                    'dateTime' => ['required', 'date'],
                ]);
                if ($v->fails()) {
                    return response()->json(['message' => 'Validation failed', 'errors' => $v->errors()], 422);
                }
                $data = $v->validated();
                $data['files'] = $data['files'] ?? [];
            } else {
                return response()->json(['error' => 'payload required (multipart with payload file or application/json body)'], 400);
            }
        }

        $item = Cashbox::create($data);
        return response()->json($item, 201);
    }

    /** Обновить запись кассы. */
    public function update(Request $request, Cashbox $cashbox)
    {
        // mirror store() behavior: support payload file, multipart with files[], or JSON
        $data = null;

        if ($request->hasFile('payload')) {
            $payloadContent = $request->file('payload')->get();
            $payload = json_decode($payloadContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'payload is not valid JSON', 'details' => json_last_error_msg()], 400);
            }

            $v = Validator::make($payload, [
                'isIncome' => ['required', 'boolean'],
                'cash_types_id' => ['required', 'integer', 'exists:cash_types,id'],
                'sum' => ['required', 'numeric'],
                'note' => ['nullable', 'string'],
                'files' => ['nullable', 'array'],
                'files.*' => ['string'],
                'dateTime' => ['required', 'date'],
            ]);
            if ($v->fails()) {
                return response()->json(['message' => 'Validation failed', 'errors' => $v->errors()], 422);
            }
            $data = $v->validated();

            $uploadedFiles = $request->file('files') ?? $request->file('files[]') ?? [];
            $savedNames = [];
            if (!empty($uploadedFiles)) {
                $maxFiles = 10;
                $maxSize = 50 * 1024 * 1024; // 50MB
                $allowedExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
                if (count($uploadedFiles) > $maxFiles) {
                    return response()->json(['error' => 'Too many files', 'max' => $maxFiles], 422);
                }
                foreach ($uploadedFiles as $f) {
                    if (! $f->isValid()) {
                        return response()->json(['error' => 'One of uploaded files is invalid'], 422);
                    }
                    if ($f->getSize() > $maxSize) {
                        return response()->json(['error' => 'File too large', 'file' => $f->getClientOriginalName()], 422);
                    }
                    $ext = strtolower($f->getClientOriginalExtension());
                    if (!in_array($ext, $allowedExt)) {
                        return response()->json(['error' => 'File type not allowed', 'file' => $f->getClientOriginalName()], 422);
                    }
                    $saved = $this->fileNamesFromUploads([$f]);
                    $savedNames = array_merge($savedNames, $saved);
                }
            }
            $data['files'] = array_merge($data['files'] ?? [], $savedNames);

        } elseif ($request->hasFile('files') || $request->hasFile('files[]')) {
            $data = $request->validate([
                'isIncome' => ['required', 'boolean'],
                'cash_types_id' => ['required', 'integer', 'exists:cash_types,id'],
                'sum' => ['required', 'numeric'],
                'note' => ['nullable', 'string'],
                'files' => ['nullable', 'array'],
                'files.*' => ['file'],
                'dateTime' => ['required', 'date'],
            ]);
            $data['files'] = $this->fileNamesFromUploads($request->file('files') ?? $request->file('files[]'));

        } else {
            if ($request->isJson() || str_contains($request->header('content-type', ''), 'application/json')) {
                $payload = $request->json()->all();
                $v = Validator::make($payload, [
                    'isIncome' => ['required', 'boolean'],
                    'cash_types_id' => ['required', 'integer', 'exists:cash_types,id'],
                    'sum' => ['required', 'numeric'],
                    'note' => ['nullable', 'string'],
                    'files' => ['nullable', 'array'],
                    'files.*' => ['string'],
                    'dateTime' => ['required', 'date'],
                ]);
                if ($v->fails()) {
                    return response()->json(['message' => 'Validation failed', 'errors' => $v->errors()], 422);
                }
                $data = $v->validated();
                $data['files'] = $data['files'] ?? [];
            } else {
                return response()->json(['error' => 'payload required (multipart with payload file or application/json body)'], 400);
            }
        }

        $cashbox->update($data);
        return response()->json($cashbox);
    }

    /** Удалить запись кассы. */
    public function destroy(Cashbox $cashbox)
    {
        $cashbox->delete();
        return response()->json(['message' => 'Удалено']);
    }

    /**
     * Сгенерировать имена из загруженных файлов: cb_YYYYmmdd_HHMMSS_filename
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
            $name = 'cb_'.$now.'_'.$safe;
            // store the uploaded file on the public disk (storage/app/public)
            // using the generated safe name so it can be served via /storage/{name}
            // save into 'cb' subdirectory on the public disk
            $file->storeAs('cb', $name, 'public');
            $names[] = $name;
        }
        return $names;
    }
}




