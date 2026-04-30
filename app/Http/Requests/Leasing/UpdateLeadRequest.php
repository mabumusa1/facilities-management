<?php

namespace App\Http\Requests\Leasing;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status_id' => [
                'required',
                'integer',
                Rule::exists('rf_statuses', 'id')->where('type', 'lead'),
            ],
            'lost_reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
