<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Notification extends Model
{
    use HasTranslations;

    protected $fillable = [
        'user_id',
        'role_name',
        'title',
        'message',
        'task_id',
        'maintenance_report_id',
        'read_at',
    ];

    public array $translatable = ['title', 'message'];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the user that receives the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the task associated with the notification.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Get the maintenance report associated with the notification.
     */
    public function maintenanceReport(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReport::class, 'maintenance_report_id');
    }
}
