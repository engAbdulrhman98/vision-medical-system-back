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
            'average_rating' => $this->averageRating(),
            'reviews_count' => $this->approvedReviews()->count(),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
