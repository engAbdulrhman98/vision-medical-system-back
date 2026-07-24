<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceRequest;
use App\Models\InvoiceRequestItem;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuotationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        if ($clients->isEmpty()) {
            return;
        }

        $seller = User::role('Sale')->first() ?? User::first();
        $accountant = User::role('Accountant')->first() ?? User::first();
        $products = Product::all();

        $statuses = ['pending', 'sent', 'accepted', 'rejected', 'expired'];
        $invStatuses = ['pending_accountant', 'issued', 'client_approved', 'collected'];

        for ($i = 1; $i <= 20; $i++) {
            $client = $clients[($i - 1) % $clients->count()];
            $status = $statuses[($i - 1) % count($statuses)];
            $amount = rand(5, 120) * 1000;
            $prod = $products->isNotEmpty() ? $products[($i - 1) % $products->count()] : null;

            $quotation = Quotation::firstOrCreate(
                ['quotation_number' => 'QT-2026-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT)],
                [
                    'client_id' => $client->id,
                    'status' => $status,
                    'total_amount' => $amount,
                    'items' => [
                        [
                            'name' => $prod ? $prod->getTranslation('name', 'ar') : 'تجهيزات أجهزة طبية',
                            'quantity' => rand(1, 5),
                            'unit_price' => $amount / 2,
                            'total_price' => $amount,
                        ]
                    ],
                    'valid_until' => now()->addDays(rand(10, 60)),
                    'notes' => 'عرض سعر مبيعات رسمي مقدم لـ ' . $client->getTranslation('name', 'ar'),
                ]
            );

            // Seed Invoices for accepted/sent quotations
            if (in_array($status, ['accepted', 'sent'])) {
                Invoice::firstOrCreate(
                    ['invoice_number' => 'INV-2026-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT)],
                    [
                        'client_id' => $client->id,
                        'quotation_id' => $quotation->id,
                        'status' => ($i % 2 === 0) ? 'paid' : 'unpaid',
                        'amount' => $amount,
                        'due_date' => now()->addDays(rand(10, 30)),
                        'notes' => 'فاتورة مبيعات رسمية صادرة للحسابات',
                    ]
                );
            }

            // Seed Invoice Requests
            $invReqStatus = $invStatuses[($i - 1) % count($invStatuses)];
            $invReq = InvoiceRequest::create([
                'user_id' => $seller ? $seller->id : 1,
                'client_id' => $client->id,
                'accountant_id' => $accountant ? $accountant->id : null,
                'collector_id' => $accountant ? $accountant->id : null,
                'request_type' => ($i % 2 === 0) ? 'sales_product' : 'maintenance_service',
                'type' => 'single',
                'total_amount' => $amount,
                'status' => $invReqStatus,
                'notes' => 'طلب تحصيل وتجهيز فاتورة رسمية من قسم الحسابات',
                'issued_at' => in_array($invReqStatus, ['issued', 'client_approved', 'collected']) ? now()->subDays(rand(2, 10)) : null,
                'collected_at' => $invReqStatus === 'collected' ? now()->subDays(rand(1, 5)) : null,
            ]);

            InvoiceRequestItem::create([
                'invoice_request_id' => $invReq->id,
                'client_id' => $client->id,
                'product_id' => $prod ? $prod->id : null,
                'item_name' => $prod ? $prod->getTranslation('name', 'ar') : 'تجهيزات ومستلزمات طبية متكاملة',
                'quantity' => rand(1, 3),
                'unit_price' => $amount,
                'total_price' => $amount,
                'invoice_number' => 'INV-REQ-2026-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
