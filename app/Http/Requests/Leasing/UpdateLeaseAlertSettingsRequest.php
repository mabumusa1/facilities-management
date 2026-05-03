<?php

namespace App\Http\Requests\Leasing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaseAlertSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'thresholds' => ['present', 'array'],
            'thresholds.*.days' => ['required', 'integer', 'min:1', 'max:365'],
            'thresholds.*.in_app' => ['required', 'boolean'],
            'thresholds.*.email' => ['required', 'boolean'],
        ];
    }
}
