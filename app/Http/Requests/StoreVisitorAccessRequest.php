<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVisitorAccessRequest extends FormRequest
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
            'unit_id' => 'nullable|exists:units,id',
            'building_id' => 'nullable|exists:buildings,id',
            'community_id' => 'nullable|exists:communities,id',
            'visitor_name' => 'required|string|max:255',
            'visitor_email' => 'nullable|email|max:255',
            'visitor_phone' => 'nullable|string|max:50',
            'visitor_id_number' => 'nullable|string|max:50',
            'visitor_vehicle_plate' => 'nullable|string|max:50',
            'visit_start_date' => 'required|date|after_or_equal:today',
            'visit_start_time' => 'nullable|date_format:H:i',
            'visit_end_date' => 'nullable|date|after_or_equal:visit_start_date',
            'visit_end_time' => 'nullable|date_format:H:i',
            'access_type' => 'required|in:one-time,recurring,permanent',
            'purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }
}
