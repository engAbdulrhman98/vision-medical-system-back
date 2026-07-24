<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->getTranslations('title'),
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'scheduled_at' => $this->scheduled_at,
            'completed_at' => $this->completed_at,
        ];
    }
}
