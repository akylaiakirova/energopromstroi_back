<?php

namespace App\Http\Controllers;

use App\Models\AvrAcceptance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * CRUD для АВР и актов приемки (avr_acceptance).
 */
class AvrAcceptanceController extends Controller
{
    /** Список АВР по дате убыванию. */
    public function index(Request $request)
    {
        $query = AvrAcceptance::with('client')->orderBy('date', 'desc');

        if ($request->filled('client_id')) {
            $query->where('client_id', (int) $request->get('client_id'));
        }

        return $query->get();
    }

    /** Создать запись АВР. */
    public function store(Request $request)
    {
        $rules = [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
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

        $item = AvrAcceptance::create($data);
        return response()->json($item->load('client'), 201);
    }

    /** Обновить запись АВР. */
    public function update(Request $request, AvrAcceptance $avr_acceptance)
    {
        $rules = [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
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

        $avr_acceptance->update($data);
        return response()->json($avr_acceptance->load('client'));
    }

    /** Удалить запись АВР. */
    public function destroy(AvrAcceptance $avr_acceptance)
    {
        $avr_acceptance->delete();
        return response()->json(['message' => 'Удалено']);
    }

    /**
     * Сгенерировать имена из загруженных файлов: avr_YYYYmmdd_HHMMSS_filename
     * Сохранение в папку avr_clients
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
            $name = 'avr_' . $now . '_' . $safe;
            // Сохраняем в папку avr_clients на публичном диске
            $file->storeAs('avr_clients', $name, 'public');
            $names[] = $name;
        }
        return $names;
    }
}
