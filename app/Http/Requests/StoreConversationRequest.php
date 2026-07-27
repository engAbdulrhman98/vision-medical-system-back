<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'is_group' => 'nullable|boolean',
            'participant_ids' => 'required_without:user_ids|array|min:1',
            'participant_ids.*' => 'integer|exists:users,id',
            'user_ids' => 'required_without:participant_ids|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ];
    }
}
