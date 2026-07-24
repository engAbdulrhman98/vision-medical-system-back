<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ClientContact extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'phone', 'job_title', 'client_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "تم {$eventName} مسؤول المستشفى/العيادة / Client contact has been {$eventName}");
    }

    protected $fillable = [
        'client_id',
        'name',
        'phone',
        'job_title',
    ];

    /**
     * Get the client (hospital/clinic) associated with this contact.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Get the tasks requested by this contact person.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'client_contact_id');
    }
}
