<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale(), false) ?: $this->name,
            'type' => $this->type,
            'area_id' => $this->area_id,
            'area' => new AreaResource($this->whenLoaded('area')),
            'governorate' => $this->governorate,
            'city' => $this->city,
            'detailed_address' => $this->detailed_address,
            'address' => $this->getTranslation('address', app()->getLocale(), false) ?: $this->address,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
