<?php

namespace App\Http\Requests;

use App\Models\LeaseApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeaseApplicationRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Applicant information
            'applicant_id' => ['nullable', 'exists:contacts,id'],
            'applicant_name' => ['sometimes', 'required', 'string', 'max:255'],
            'applicant_email' => ['sometimes', 'required', 'email', 'max:255'],
            'applicant_phone' => ['nullable', 'string', 'max:50'],
            'applicant_type' => ['sometimes', 'required', Rule::in(['individual', 'company'])],
            'company_name' => ['nullable', 'string', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:20'],
            'commercial_registration' => ['nullable', 'string', 'max:50'],

            // Property selection
            'community_id' => ['nullable', 'exists:communities,id'],
            'building_id' => ['nullable', 'exists:buildings,id'],

            // Units selection
            'units' => ['nullable', 'array'],
            'units.*.id' => ['required', 'exists:units,id'],
            'units.*.proposed_rental_amount' => ['nullable', 'numeric', 'min:0'],
            'units.*.net_area' => ['nullable', 'numeric', 'min:0'],
            'units.*.meter_cost' => ['nullable', 'numeric', 'min:0'],

            // Financial terms
            'quoted_rental_amount' => ['nullable', 'numeric', 'min:0'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],

            // Dates
            'proposed_start_date' => ['nullable', 'date'],
            'proposed_end_date' => ['nullable', 'date', 'after:proposed_start_date'],
            'proposed_duration_months' => ['nullable', 'integer', 'min:1', 'max:120'],

            // Additional details
            'special_terms' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', Rule::in([
                LeaseApplication::SOURCE_WALK_IN,
                LeaseApplication::SOURCE_WEBSITE,
                LeaseApplication::SOURCE_REFERRAL,
                LeaseApplication::SOURCE_MARKETPLACE,
            ])],

            // Assignment
            'assigned_to_id' => ['nullable', 'exists:contacts,id'],
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
            'applicant_name.required' => 'Please provide the applicant name.',
            'applicant_email.required' => 'Please provide the applicant email address.',
            'applicant_email.email' => 'Please provide a valid email address.',
            'applicant_type.required' => 'Please specify whether the applicant is an individual or company.',
            'proposed_end_date.after' => 'The end date must be after the start date.',
        ];
    }
}
