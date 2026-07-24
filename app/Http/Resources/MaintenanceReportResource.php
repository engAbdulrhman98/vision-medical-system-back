<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'summary' => $this->getTranslations('summary'),
            'findings' => $this->findings,
            'actions_taken' => $this->actions_taken,
            'status' => $this->status,
        ];
    }
}
