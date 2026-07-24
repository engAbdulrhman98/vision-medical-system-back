<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Quotation extends Model
{
    use LogsActivity;

    /**
     * Configure activity logging for the Quotation model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['client_id', 'quotation_number', 'status', 'total_amount', 'valid_until'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "تم {$eventName} طلب عرض السعر / Quotation has been {$eventName}");
    }

    protected $fillable = [
        'client_id',
        'quotation_number',
        'status',
        'total_amount',
        'items',
        'valid_until',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'items' => 'array',
            'valid_until' => 'date',
        ];
    }

    /**
     * Get the client associated with this quotation.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
