<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'device_id',
        'scheduled_date',
        'type',
        'status',
        'notes',
    ];

    /**
     * Get the device associated with this maintenance schedule.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
