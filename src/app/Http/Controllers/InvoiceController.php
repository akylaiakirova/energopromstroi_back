<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CRUD для накладных (invoices) + товары накладной (invoice_products).
 * В create/update товары принимаются массивом и сохраняются атомарно.
 */
class InvoiceController extends Controller
{
    /** Список накладных с товарами (и связями client/contract). */
    public function index(Request $request)
    {
        $query = Invoice::with(['products', 'client', 'contract'])->orderBy('date', 'desc');

        if ($request->filled('client_id')) {
            $query->where('client_id', (int) $request->get('client_id'));
        }
        if ($request->filled('contract_id')) {
            $query->where('contract_id', (int) $request->get('contract_id'));
        }

        return $query->get();
    }

    /** Создать накладную с товарами. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'number' => ['required', 'string', 'max:255'],
            'contract_id' => ['nullable', 'integer', 'exists:contracts,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'files' => ['nullable', 'array'],
            'files.*' => ['string'],
            'note' => ['nullable', 'string'],
            'invoice_products' => ['required', 'array', 'min:1'],
            'invoice_products.*.product_name' => ['required', 'string', 'max:255'],
            'invoice_products.*.count' => ['required', 'integer', 'min:1'],
            'invoice_products.*.price_for_1' => ['required', 'numeric', 'min:0'],
            'invoice_products.*.total_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($data) {
            $invoice = Invoice::create([
                'date' => $data['date'],
                'number' => $data['number'],
                'contract_id' => $data['contract_id'] ?? null,
                'client_id' => $data['client_id'] ?? null,
                'address' => $data['address'] ?? null,
                'files' => $data['files'] ?? [],
                'note' => $data['note'] ?? null,
            ]);

            $productsPayload = [];
            foreach ($data['invoice_products'] as $row) {
                $count = (int) $row['count'];
                $price = (float) $row['price_for_1'];
                $total = array_key_exists('total_price', $row) && $row['total_price'] !== null
                    ? (float) $row['total_price']
                    : ($count * $price);

                $productsPayload[] = [
                    'invoice_id' => $invoice->id,
                    'product_name' => $row['product_name'],
                    'count' => $count,
                    'price_for_1' => $price,
                    'total_price' => $total,
                ];
            }
            InvoiceProduct::insert($productsPayload);

            return response()->json($invoice->load(['products', 'client', 'contract']), 201);
        });
    }

    /** Обновить накладную и перезаписать товары массивом. */
    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'number' => ['required', 'string', 'max:255'],
            'contract_id' => ['nullable', 'integer', 'exists:contracts,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'files' => ['nullable', 'array'],
            'files.*' => ['string'],
            'note' => ['nullable', 'string'],
            'invoice_products' => ['required', 'array', 'min:1'],
            'invoice_products.*.product_name' => ['required', 'string', 'max:255'],
            'invoice_products.*.count' => ['required', 'integer', 'min:1'],
            'invoice_products.*.price_for_1' => ['required', 'numeric', 'min:0'],
            'invoice_products.*.total_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($invoice, $data) {
            $invoice->update([
                'date' => $data['date'],
                'number' => $data['number'],
                'contract_id' => $data['contract_id'] ?? null,
                'client_id' => $data['client_id'] ?? null,
                'address' => $data['address'] ?? null,
                'files' => $data['files'] ?? [],
                'note' => $data['note'] ?? null,
            ]);

            InvoiceProduct::where('invoice_id', $invoice->id)->delete();

            $productsPayload = [];
            foreach ($data['invoice_products'] as $row) {
                $count = (int) $row['count'];
                $price = (float) $row['price_for_1'];
                $total = array_key_exists('total_price', $row) && $row['total_price'] !== null
                    ? (float) $row['total_price']
                    : ($count * $price);

                $productsPayload[] = [
                    'invoice_id' => $invoice->id,
                    'product_name' => $row['product_name'],
                    'count' => $count,
                    'price_for_1' => $price,
                    'total_price' => $total,
                ];
            }
            InvoiceProduct::insert($productsPayload);

            return response()->json($invoice->load(['products', 'client', 'contract']));
        });
    }

    /** Удалить накладную (товары удалятся каскадом). */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(['message' => 'Удалено']);
    }
}


