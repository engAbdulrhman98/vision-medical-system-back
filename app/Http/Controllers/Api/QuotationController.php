<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Http\Resources\QuotationResource;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class QuotationController extends Controller
{
    /**
     * Display a listing of the quotations.
     */
    public function index()
    {
        $quotations = QueryBuilder::for(Quotation::class)
            ->allowedFilters(
                'client_id',
                'quotation_number',
                'status',
                AllowedFilter::callback('search', function (Builder $query, $value) {
                    $query->where(function (Builder $q) use ($value) {
                        $q->where('quotation_number', 'like', "%{$value}%")
                          ->orWhere('status', 'like', "%{$value}%")
                          ->orWhere('notes', 'like', "%{$value}%");
                    });
                })
            )
            ->allowedSorts('quotation_number', 'total_amount', 'valid_until', 'created_at')
            ->allowedIncludes('client')
            ->latest()
            ->paginate(10);

        return QuotationResource::collection($quotations);
    }

    /**
     * Store a newly created quotation in storage.
     */
    public function store(StoreQuotationRequest $request)
    {
        $validated = $request->validated();

        $quotation = Quotation::create($validated);

        return response()->json([
            'message' => 'Quotation created successfully',
            'quotation' => new QuotationResource($quotation),
        ], 201);
    }

    /**
     * Display the specified quotation.
     */
    public function show(Quotation $quotation)
    {
        $quotation->load('client');
        return new QuotationResource($quotation);
    }

    /**
     * Update the specified quotation in storage.
     */
    public function update(UpdateQuotationRequest $request, Quotation $quotation)
    {
        $validated = $request->validated();

        $quotation->update($validated);
        $quotation->load('client');

        return response()->json([
            'message' => 'Quotation updated successfully',
            'quotation' => new QuotationResource($quotation),
        ]);
    }

    /**
     * Remove the specified quotation from storage.
     */
    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return response()->json([
            'message' => 'Quotation deleted successfully',
        ]);
    }
}
