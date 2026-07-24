<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'role_name' => $this->role_name,
            'title' => $this->getTranslations('title'),
            'message' => $this->getTranslations('message'),
            'task_id' => $this->task_id,
            'task' => new TaskResource($this->whenLoaded('task')),
            'maintenance_report_id' => $this->maintenance_report_id,
            'maintenance_report' => new MaintenanceReportResource($this->whenLoaded('maintenanceReport')),
            'read_at' => $this->read_at ? $this->read_at->toDateTimeString() : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
