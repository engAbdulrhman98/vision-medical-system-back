<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|integer|exists:clients,id',
            'quotation_number' => 'nullable|string|unique:quotations,quotation_number',
            'status' => 'nullable|string|in:pending,sent,accepted,rejected,expired',
            'total_amount' => 'nullable|numeric|min:0',
            'items' => 'nullable|array',
            'items.*.name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'valid_until' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ];
    }
}
