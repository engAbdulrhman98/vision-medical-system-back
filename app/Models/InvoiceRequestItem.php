<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceRequestItem extends Model
{
    protected $fillable = [
        'invoice_request_id',
        'client_id',
        'product_id',
        'item_name',
        'quantity',
        'unit_price',
        'total_price',
        'invoice_number',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'double',
        'total_price' => 'double',
    ];

    public function invoiceRequest(): BelongsTo
    {
        return $this->belongsTo(InvoiceRequest::class, 'invoice_request_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
