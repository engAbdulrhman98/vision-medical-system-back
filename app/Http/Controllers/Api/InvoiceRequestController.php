<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvoiceRequest;
use App\Models\InvoiceRequestItem;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceRequestController extends Controller
{
    /**
     * Display a listing of invoice requests.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = InvoiceRequest::with(['user', 'client', 'accountant', 'collector', 'items.client', 'items.product'])
            ->latest();

        // Role-based filtering if role parameter or user role exists
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('my_requests')) {
            $query->where('user_id', $user->id);
        }

        $requests = $query->paginate($request->get('per_page', 20));

        return response()->json($requests);
    }

    /**
     * Store a newly created invoice request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_type' => 'required|string|in:maintenance_service,sales_product',
            'type' => 'required|string|in:single,batch',
            'client_id' => 'nullable|exists:clients,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.client_id' => 'nullable|exists:clients,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $totalAmount = 0.0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $invoiceRequest = InvoiceRequest::create([
                'user_id' => $request->user()->id,
                'client_id' => $validated['client_id'] ?? null,
                'request_type' => $validated['request_type'],
                'type' => $validated['type'],
                'total_amount' => $totalAmount,
                'status' => 'pending_accountant',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $itemTotal = $itemData['quantity'] * $itemData['unit_price'];
                InvoiceRequestItem::create([
                    'invoice_request_id' => $invoiceRequest->id,
                    'client_id' => $itemData['client_id'] ?? $validated['client_id'] ?? null,
                    'product_id' => $itemData['product_id'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemTotal,
                ]);
            }

            // Notify Accountants
            $accountantIds = User::whereHas('roles', function($q) {
                $q->where('name', 'like', '%accountant%');
            })->pluck('id');

            if ($accountantIds->isEmpty()) {
                // Fallback: Notify Admins
                $accountantIds = User::whereHas('roles', function($q) {
                    $q->where('name', 'like', '%admin%');
                })->pluck('id');
            }

            $requesterName = $request->user()->name;
            $typeLabel = $validated['request_type'] === 'maintenance_service' ? 'صيانة خارجية' : 'مبيعات أجهزة';

            foreach ($accountantIds as $accId) {
                Notification::create([
                    'user_id' => $accId,
                    'type' => 'invoice_requested',
                    'title' => [
                        'ar' => "طلب فاتورة جديد ($typeLabel)",
                        'en' => "New Invoice Request ($typeLabel)",
                    ],
                    'message' => [
                        'ar' => "قام $requesterName بطلب فاتورة جديدة رقم #{$invoiceRequest->id} بمبلغ {$totalAmount} ج.م",
                        'en' => "$requesterName requested a new invoice #{$invoiceRequest->id} for {$totalAmount} EGP",
                    ],
                    'data' => [
                        'invoice_request_id' => $invoiceRequest->id,
                    ],
                ]);
            }

            return response()->json([
                'message' => 'Invoice request submitted successfully.',
                'data' => $invoiceRequest->load(['items', 'client']),
            ], 201);
        });
    }

    /**
     * Issue invoice official documents by Accountant.
     */
    public function issueInvoices(Request $request, $id)
    {
        $invoiceRequest = InvoiceRequest::with('items')->findOrFail($id);

        if ($invoiceRequest->status !== 'pending_accountant') {
            return response()->json(['message' => 'Invoice request has already been processed.'], 400);
        }

        return DB::transaction(function () use ($request, $invoiceRequest) {
            $invoiceRequest->update([
                'status' => 'issued',
                'accountant_id' => $request->user()->id,
                'issued_at' => now(),
            ]);

            // Create official Invoice model records for items
            foreach ($invoiceRequest->items as $item) {
                $invNumber = 'INV-' . date('Y') . '-' . str_pad($item->id, 5, '0', STR_PAD_LEFT);
                $item->update(['invoice_number' => $invNumber]);

                Invoice::create([
                    'invoice_number' => $invNumber,
                    'client_id' => $item->client_id ?? $invoiceRequest->client_id,
                    'amount' => $item->total_price,
                    'status' => 'Pending',
                    'due_date' => now()->addDays(15),
                    'notes' => "صادرة عن طلب فاتورة #{$invoiceRequest->id} ({$item->item_name})",
                ]);
            }

            // Notify Creator (Outdoor Engineer / Sales Rep)
            Notification::create([
                'user_id' => $invoiceRequest->user_id,
                'type' => 'invoice_issued',
                'title' => [
                    'ar' => 'تم إصدار الفاتورة رسمياً',
                    'en' => 'Invoice Issued Officially',
                ],
                'message' => [
                    'ar' => "قام المحاسب بإصدار الفاتورة الخاصة بطلبك رقم #{$invoiceRequest->id}. يمكنك الآن عرضها ورئاسياً أو تقديمها للعميل.",
                    'en' => "The accountant has issued the invoice for your request #{$invoiceRequest->id}. You can now present it to the client.",
                ],
                'data' => [
                    'invoice_request_id' => $invoiceRequest->id,
                ],
            ]);

            return response()->json([
                'message' => 'Invoices issued successfully.',
                'data' => $invoiceRequest->load('items'),
            ]);
        });
    }

    /**
     * Record client response (Approved or Rejected) by Field Rep.
     */
    public function respondByClient(Request $request, $id)
    {
        $validated = $request->validate([
            'response' => 'required|string|in:approved,rejected',
            'rejection_reason' => 'nullable|string',
        ]);

        $invoiceRequest = InvoiceRequest::with(['client', 'user'])->findOrFail($id);

        if ($validated['response'] === 'approved') {
            $invoiceRequest->update([
                'status' => 'ready_for_collection',
                'client_responded_at' => now(),
            ]);

            // Notify Collectors
            $collectorIds = User::whereHas('roles', function($q) {
                $q->where('name', 'like', '%collector%')
                  ->orWhere('name', 'like', '%accountant%');
            })->pluck('id');

            $clientName = $invoiceRequest->client ? $invoiceRequest->client->name : 'العميل';

            foreach ($collectorIds as $colId) {
                Notification::create([
                    'user_id' => $colId,
                    'type' => 'invoice_approved_for_collection',
                    'title' => [
                        'ar' => 'فاتورة جاهزة للتحصيل الميداني 💰',
                        'en' => 'Invoice Ready for Field Collection 💰',
                    ],
                    'message' => [
                        'ar' => "وافق العميل ($clientName) على الفاتورة رقم #{$invoiceRequest->id}. المبلغ المطلوب للتحصيل: {$invoiceRequest->total_amount} ج.م",
                        'en' => "Client ($clientName) approved invoice #{$invoiceRequest->id}. Collection amount: {$invoiceRequest->total_amount} EGP",
                    ],
                    'data' => [
                        'invoice_request_id' => $invoiceRequest->id,
                    ],
                ]);
            }
        } else {
            $invoiceRequest->update([
                'status' => 'client_rejected',
                'rejection_reason' => $validated['rejection_reason'] ?? 'رفض العميل بدون ذكر سبب',
                'client_responded_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Client response recorded successfully.',
            'data' => $invoiceRequest,
        ]);
    }

    /**
     * Confirm collection by Collector.
     */
    public function markCollected(Request $request, $id)
    {
        $invoiceRequest = InvoiceRequest::findOrFail($id);

        $invoiceRequest->update([
            'status' => 'collected',
            'collector_id' => $request->user()->id,
            'collected_at' => now(),
        ]);

        return response()->json([
            'message' => 'Payment collected successfully.',
            'data' => $invoiceRequest,
        ]);
    }
}
