<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitorAccessRequest extends FormRequest
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
            'unit_id' => 'sometimes|nullable|exists:units,id',
            'building_id' => 'sometimes|nullable|exists:buildings,id',
            'community_id' => 'sometimes|nullable|exists:communities,id',
            'visitor_name' => 'sometimes|string|max:255',
            'visitor_email' => 'sometimes|nullable|email|max:255',
            'visitor_phone' => 'sometimes|nullable|string|max:50',
            'visitor_id_number' => 'sometimes|nullable|string|max:50',
            'visitor_vehicle_plate' => 'sometimes|nullable|string|max:50',
            'visit_start_date' => 'sometimes|date',
            'visit_start_time' => 'sometimes|nullable|date_format:H:i',
            'visit_end_date' => 'sometimes|nullable|date|after_or_equal:visit_start_date',
            'visit_end_time' => 'sometimes|nullable|date_format:H:i',
            'access_type' => 'sometimes|in:one-time,recurring,permanent',
            'purpose' => 'sometimes|nullable|string|max:255',
            'notes' => 'sometimes|nullable|string',
        ];
    }
}
