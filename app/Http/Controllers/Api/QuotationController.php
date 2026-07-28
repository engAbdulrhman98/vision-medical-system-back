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

use App\Models\User;
use App\Models\Notification;

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
        $quotation->load('client');

        // Notify Accountants & Admins about the new quotation request
        $accountantUserIds = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Accountant', 'Admin', 'CEO']);
        })->orWhereIn('role', ['accountant', 'admin', 'ceo'])->pluck('id');

        $requesterName = $request->user() ? $request->user()->name : 'مهندس الصيانة الخارجي';
        $clientName = $quotation->client ? $quotation->client->name : 'عميل محدد';

        foreach ($accountantUserIds as $accId) {
            Notification::create([
                'user_id' => $accId,
                'type' => 'quotation_request',
                'title' => [
                    'ar' => "طلب عرض سعر جديد ({$quotation->quotation_number})",
                    'en' => "New Quotation Request ({$quotation->quotation_number})",
                ],
                'message' => [
                    'ar' => "قام $requesterName بإرسال طلب عرض سعر جديد للعميل ($clientName). يرجى المراجعة والتسعير.",
                    'en' => "$requesterName submitted a quotation request for $clientName. Please review and price.",
                ],
                'data' => [
                    'quotation_id' => $quotation->id,
                    'quotation_number' => $quotation->quotation_number,
                    'client_name' => $clientName,
                ]
            ]);
        }

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

        $statusLabelAr = match($quotation->status) {
            'approved' => 'مقبول ومعتمد',
            'rejected' => 'مرفوض',
            'sent' => 'مكتمل ومُرسل للعميل',
            default => 'مُحدّث'
        };

        $statusLabelEn = match($quotation->status) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'sent' => 'Sent to Client',
            default => 'Updated'
        };

        // Notify Engineers & Sellers about quotation pricing/status update
        $fieldUserIds = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Service Engineer outdoor', 'Service Engineer indoor', 'Sale', 'Admin', 'CEO']);
        })->orWhereIn('role', ['engineer', 'seller', 'admin', 'ceo'])->pluck('id');

        $updaterName = $request->user() ? $request->user()->name : 'المحاسب';
        $formattedAmount = number_format($quotation->total_amount, 2);

        foreach ($fieldUserIds as $fId) {
            Notification::create([
                'user_id' => $fId,
                'type' => 'quotation_updated',
                'title' => [
                    'ar' => "اعتماد/تحديث عرض السعر ({$quotation->quotation_number})",
                    'en' => "Quotation Status Updated ({$quotation->quotation_number})",
                ],
                'message' => [
                    'ar' => "قام $updaterName بتحديث عرض السعر للعميل (" . ($quotation->client?->name ?? 'العميل') . ") إلى: [$statusLabelAr] - المجموع: $formattedAmount ر.س",
                    'en' => "$updaterName updated quotation {$quotation->quotation_number} status to [$statusLabelEn] - Total: $formattedAmount SAR",
                ],
                'data' => [
                    'quotation_id' => $quotation->id,
                    'quotation_number' => $quotation->quotation_number,
                    'status' => $quotation->status,
                    'total_amount' => $quotation->total_amount,
                ]
            ]);
        }

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
