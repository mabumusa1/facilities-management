<?php

namespace App\Http\Requests\Leasing;

use Illuminate\Foundation\Http\FormRequest;

class QuoteReviseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('leases.UPDATE') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'unit_id' => ['required', 'exists:rf_units,id'],
            'contact_id' => ['required', 'exists:rf_tenants,id'],
            'contract_type_id' => ['nullable', 'exists:rf_contract_types,id'],
            'duration_months' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'rent_amount' => ['required', 'numeric', 'min:0'],
            'payment_frequency_id' => ['required', 'exists:rf_settings,id'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'valid_until' => ['required', 'date', 'after:today'],
            'additional_charges' => ['nullable', 'array'],
            'additional_charges.*.label' => ['required_with:additional_charges', 'array'],
            'additional_charges.*.label.en' => ['required_with:additional_charges', 'string', 'max:255'],
            'additional_charges.*.label.ar' => ['required_with:additional_charges', 'string', 'max:255'],
            'additional_charges.*.amount' => ['required_with:additional_charges', 'numeric', 'min:0'],
            'special_conditions' => ['nullable', 'array'],
            'special_conditions.en' => ['nullable', 'string'],
            'special_conditions.ar' => ['nullable', 'string'],
            'revision_note' => ['nullable', 'string', 'max:2000'],
            'email_subject_prefix' => ['nullable', 'string', 'max:255'],
        ];
    }
}
