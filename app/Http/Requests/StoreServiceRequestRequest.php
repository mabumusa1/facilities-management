<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequestRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:service_request_categories,id',
            'subcategory_id' => 'nullable|exists:service_request_subcategories,id',
            'status_id' => 'nullable|exists:statuses,id',
            'community_id' => 'nullable|exists:communities,id',
            'building_id' => 'nullable|exists:buildings,id',
            'unit_id' => 'nullable|exists:units,id',
            'requester_type' => 'required|in:Owner,Tenant,Admin',
            'professional_id' => 'nullable|exists:contacts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'is_all_day' => 'boolean',
            'estimated_cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'attachments' => 'nullable|array',
            'notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ];
    }
}
