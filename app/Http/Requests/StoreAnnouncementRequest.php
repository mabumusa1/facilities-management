<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'end_time' => ['required', 'date_format:H:i'],
            'is_visible' => ['boolean'],
            'notify_user_types' => ['nullable', 'array'],
            'notify_user_types.*' => ['string', 'in:tenant,owner,all'],
            'community_ids' => ['nullable', 'array'],
            'community_ids.*' => ['integer', 'exists:communities,id'],
            'building_ids' => ['nullable', 'array'],
            'building_ids.*' => ['integer', 'exists:buildings,id'],
            'priority' => ['required', 'string', 'in:low,normal,high,urgent'],
            'status' => ['nullable', 'string', 'in:draft,scheduled,active'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'notify_user_types.*.in' => 'Invalid user type selected.',
            'priority.in' => 'Invalid priority level selected.',
        ];
    }
}
