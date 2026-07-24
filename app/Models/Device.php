<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Device extends Model
{
    use LogsActivity;

    /**
     * Configure activity logging for the Device model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['serial_number', 'client_id', 'product_id', 'status', 'installation_date'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "تم {$eventName} الجهاز / Device has been {$eventName}");
    }
    protected $fillable = [
        'serial_number',
        'client_id',
        'product_id',
        'status',
        'installation_date',
    ];

    /**
     * Get the client that owns the device.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Get the commercial product this device is an instance of.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the tasks scheduled for this device.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'device_id');
    }
}
