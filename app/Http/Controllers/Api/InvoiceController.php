<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Http\Resources\InvoiceResource;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     */
    public function index()
    {
        $invoices = QueryBuilder::for(Invoice::class)
            ->allowedFilters(
                'client_id',
                'quotation_id',
                'status',
                AllowedFilter::callback('search', function (Builder $query, $value) {
                    $query->where(function (Builder $q) use ($value) {
                        $q->where('invoice_number', 'like', "%{$value}%")
                          ->orWhere('status', 'like', "%{$value}%")
                          ->orWhere('notes', 'like', "%{$value}%");
                    });
                })
            )
            ->allowedSorts('invoice_number', 'amount', 'due_date', 'created_at')
            ->allowedIncludes('client', 'quotation')
            ->latest()
            ->paginate(request('per_page', 100));

        return InvoiceResource::collection($invoices);
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'quotation_id' => 'nullable|integer|exists:quotations,id',
            'invoice_number' => 'nullable|string|unique:invoices,invoice_number',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:unpaid,paid,partially_paid',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['invoice_number'])) {
            $validated['invoice_number'] = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
        }

        $invoice = Invoice::create($validated);
        $invoice->load(['client', 'quotation']);

        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => new InvoiceResource($invoice),
        ], 201);
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'quotation']);
        return new InvoiceResource($invoice);
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|integer|exists:clients,id',
            'quotation_id' => 'nullable|integer|exists:quotations,id',
            'invoice_number' => 'nullable|string|unique:invoices,invoice_number,' . $invoice->id,
            'amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:unpaid,paid,partially_paid',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);
        $invoice->load(['client', 'quotation']);

        return response()->json([
            'message' => 'Invoice updated successfully',
            'invoice' => new InvoiceResource($invoice),
        ]);
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully',
        ]);
    }
}
