<?php

namespace App\Http\Requests;

use App\Models\Tenant;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FacilityCalendarBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is delegated to the controller via $this->authorize('create', FacilityBooking::class).
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
        $tenantId = Tenant::current()?->id;

        return [
            'facility_id' => [
                'required',
                'integer',
                Rule::exists('rf_facilities', 'id')->where('account_tenant_id', $tenantId),
            ],
            'booking_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'resident_id' => [
                'nullable',
                'integer',
                Rule::exists('rf_residents', 'id')->where('account_tenant_id', $tenantId),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
