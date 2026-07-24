<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'client_id' => $this->client_id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'quotation_id' => $this->quotation_id,
            'quotation' => new QuotationResource($this->whenLoaded('quotation')),
            'amount' => $this->amount,
            'status' => $this->status,
            'due_date' => $this->due_date ? $this->due_date->toDateString() : null,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
