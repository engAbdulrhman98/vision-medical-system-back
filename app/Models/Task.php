<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Task extends Model
{
    use HasTranslations, LogsActivity;

    /**
     * Configure activity logging for the Task model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'status', 'priority', 'user_id', 'scheduled_at', 'completed_at'])
            ->logOnlyDirty() // Log only changed attributes
            ->dontLogEmptyChanges() // Avoid logging if nothing changed
            ->setDescriptionForEvent(fn(string $eventName) => "تم {$eventName} المهمة / Task has been {$eventName}");
    }

    protected $fillable = [
        'title',
        'description',
        'status',
        'progress',
        'priority',
        'type',
        'action_type',
        'rejection_reason',
        'accountant_note',
        'invoice_id',
        'device_id',
        'client_id',
        'client_contact_id',
        'governorate_id',
        'city_id',
        'user_id',
        'scheduled_at',
        'completed_at',
        'otp_code',
        'otp_expires_at',
        'otp_verified_at',
    ];

    public array $translatable = ['title'];

    /**
     * Get the device associated with the task.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    /**
     * Get the client associated with the task.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Get the client contact person who requested or oversees this task.
     */
    public function clientContact(): BelongsTo
    {
        return $this->belongsTo(ClientContact::class, 'client_contact_id');
    }

    /**
     * Get the governorate associated with the task.
     */
    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'governorate_id');
    }

    /**
     * Get the city associated with the task.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'city_id');
    }

    /**
     * Get the technician/engineer user assigned to this task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the maintenance report completed for this task.
     */
    public function maintenanceReport(): HasOne
    {
        return $this->hasOne(MaintenanceReport::class, 'task_id');
    }

    /**
     * Get the notifications related to the task.
     */
    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Notification::class, 'task_id');
    }

    /**
     * Get the progress updates related to the task.
     */
    public function updates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TaskUpdate::class, 'task_id');
    }
}
