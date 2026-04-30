<?php

namespace App\Http\Requests\Leasing;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeaseRenewalOfferRequest extends FormRequest
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
            'new_start_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:240'],
            'new_rent_amount' => ['required', 'numeric', 'min:0'],
            'payment_frequency' => ['nullable', 'string', 'max:100'],
            'contract_type_id' => ['nullable', 'integer', 'exists:rf_settings,id'],
            'valid_until' => ['required', 'date', 'after:today'],
            'message_en' => ['nullable', 'string', 'max:5000'],
            'message_ar' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
