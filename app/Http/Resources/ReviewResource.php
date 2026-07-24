<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product ? $this->product->getTranslations('name') : null,
            'reviewer_name' => $this->reviewer_name,
            'rating' => (int) $this->rating,
            'comment' => $this->comment,
            'is_approved' => (bool) $this->is_approved,
            'created_at' => $this->created_at,
        ];
    }
}
