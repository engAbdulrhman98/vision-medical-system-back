<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
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
            'user_id' => 'required_without:role_name|nullable|integer|exists:users,id',
            'role_name' => 'required_without:user_id|nullable|string',
            'title' => 'required|array',
            'title.ar' => 'required|string|max:255',
            'title.en' => 'required|string|max:255',
            'message' => 'required|array',
            'message.ar' => 'required|string',
            'message.en' => 'required|string',
            'task_id' => 'nullable|integer|exists:tasks,id',
            'maintenance_report_id' => 'nullable|integer|exists:maintenance_reports,id',
        ];
    }
}
