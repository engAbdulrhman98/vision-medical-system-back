<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'slug' => $this->slug,
            'description' => $this->getTranslations('description'),
            'image' => $this->image,
            'products_count' => $this->products_count ?? $this->products()->count(),
        ];
    }
}
