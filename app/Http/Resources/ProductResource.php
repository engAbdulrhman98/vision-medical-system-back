<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->getTranslations('description'),
            'details' => $this->getTranslations('details'),
            'price' => (float) $this->price,
            'image' => $this->image,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'in_stock' => (bool) $this->in_stock,
            'average_rating' => isset($this->average_rating) ? round((float)$this->average_rating, 1) : 5.0,
            'reviews_count' => isset($this->approved_reviews_count) ? (int)$this->approved_reviews_count : 0,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
