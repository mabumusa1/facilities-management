<?php

namespace App\Http\Requests\Leasing;

use App\Models\Lease;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeaseAmendmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $lease = $this->route('lease');

        if (! $lease instanceof Lease) {
            return false;
        }

        return $this->user()->can('amend', $lease);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'end_date' => ['nullable', 'date', 'after:today'],
            'rental_total_amount' => ['nullable', 'numeric', 'min:0'],
            'rental_contract_type_id' => ['nullable', 'integer', 'exists:rf_settings,id'],
            'payment_schedule_id' => ['nullable', 'integer', 'exists:rf_settings,id'],
            'security_deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'terms_conditions' => ['nullable', 'string'],
            'units' => ['nullable', 'array'],
            'units.*.id' => ['required_with:units', 'integer', 'exists:rf_units,id'],
            'units.*.rental_amount' => ['nullable', 'numeric', 'min:0'],
            'units.*.annual_rental_amount' => ['nullable', 'numeric', 'min:0'],
            'units.*.rental_annual_type' => ['nullable', 'string', 'max:255'],
            'units.*.net_area' => ['nullable', 'numeric', 'min:0'],
            'units.*.meter_cost' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'min:5'],
        ];
    }
}
