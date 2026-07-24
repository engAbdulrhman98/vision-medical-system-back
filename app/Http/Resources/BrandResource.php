<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->getTranslations('name'),
            'slug'           => $this->slug,
            'image'          => $this->image_url,
            'image_url'      => $this->image_url,
            'thumb_url'      => $this->thumb_url,
            'products_count' => $this->products_count ?? 0,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
