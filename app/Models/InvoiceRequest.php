<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'accountant_id',
        'collector_id',
        'request_type',
        'type',
        'total_amount',
        'status',
        'notes',
        'rejection_reason',
        'issued_at',
        'client_responded_at',
        'collected_at',
    ];

    protected $casts = [
        'total_amount' => 'double',
        'issued_at' => 'datetime',
        'client_responded_at' => 'datetime',
        'collected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function accountant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accountant_id');
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceRequestItem::class, 'invoice_request_id');
    }
}
