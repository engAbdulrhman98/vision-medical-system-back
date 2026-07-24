<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceReport extends Model
{
    use HasTranslations;

    protected $fillable = [
        'task_id',
        'summary',
        'findings',
        'actions_taken',
        'status',
    ];

    public array $translatable = ['summary'];

    /**
     * Get the task that this report documents.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Get the notifications related to this maintenance report.
     */
    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Notification::class, 'maintenance_report_id');
    }
}
