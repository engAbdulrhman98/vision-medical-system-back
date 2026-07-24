<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->description,
            'user' => $this->causer ? $this->causer->name : 'System',
            'created_at' => $this->created_at,
        ];
    }
}
