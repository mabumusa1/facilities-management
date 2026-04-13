<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeaseRequest extends FormRequest
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
            // Core identifiers
            'tenant_id' => ['required', 'integer', 'exists:contacts,id'],
            'community_id' => ['nullable', 'integer', 'exists:communities,id'],
            'building_id' => ['nullable', 'integer', 'exists:buildings,id'],

            // Units
            'units' => ['required', 'array', 'min:1'],
            'units.*.id' => ['required', 'integer', 'exists:units,id'],
            'units.*.rental_annual_type' => ['nullable', 'string'],
            'units.*.annual_rental_amount' => ['nullable', 'numeric', 'min:0'],
            'units.*.net_area' => ['nullable', 'numeric', 'min:0'],
            'units.*.meter_cost' => ['nullable', 'numeric', 'min:0'],

            // Contract details
            'contract_number' => ['nullable', 'string', 'max:50'],
            'tenant_type' => ['required', 'string', 'in:individual,company'],
            'rental_type' => ['nullable', 'string', 'in:summary,detailed'],

            // Financial
            'rental_total_amount' => ['required', 'numeric', 'min:0'],
            'security_deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'security_deposit_due_date' => ['nullable', 'date'],

            // Dates
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'handover_date' => ['nullable', 'date'],

            // Duration (optional - can be calculated from dates)
            'number_of_years' => ['nullable', 'integer', 'min:0'],
            'number_of_months' => ['nullable', 'integer', 'min:0', 'max:11'],
            'number_of_days' => ['nullable', 'integer', 'min:0', 'max:30'],
            'free_period' => ['nullable', 'integer', 'min:0'],

            // Escalation & fees
            'lease_escalations_type' => ['nullable', 'string'],
            'lease_escalations' => ['nullable', 'array'],
            'additional_fees_lease' => ['nullable', 'array'],

            // Terms
            'terms_conditions' => ['nullable', 'string'],
            'is_terms' => ['boolean'],

            // Other references
            'deal_owner_id' => ['nullable', 'integer', 'exists:contacts,id'],
            'lease_unit_type_id' => ['nullable', 'integer'],
            'rental_contract_type_id' => ['nullable', 'integer'],
            'payment_schedule_id' => ['nullable', 'integer'],
            'legal_representative' => ['nullable', 'string', 'max:255'],
            'fit_out_status' => ['nullable', 'string'],
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
            'tenant_id.required' => 'A tenant must be selected.',
            'tenant_id.exists' => 'The selected tenant does not exist.',
            'units.required' => 'At least one unit must be selected.',
            'units.min' => 'At least one unit must be selected.',
            'units.*.id.exists' => 'One of the selected units does not exist.',
            'start_date.required' => 'The lease start date is required.',
            'end_date.required' => 'The lease end date is required.',
            'end_date.after' => 'The end date must be after the start date.',
            'rental_total_amount.required' => 'The total rental amount is required.',
            'rental_total_amount.min' => 'The rental amount must be positive.',
        ];
    }
}
