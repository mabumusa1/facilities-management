<?php

namespace App\Http\Requests\Leasing;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreLeadRequest extends FormRequest
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
            'name_en' => ['nullable', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'phone_country_code' => ['nullable', 'string', 'max:5'],
            'phone_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'source_id' => [
                'required',
                'integer',
                Rule::exists('rf_lead_sources', 'id'),
            ],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $nameEn = $this->input('name_en');
                $nameAr = $this->input('name_ar');

                if (empty($nameEn) && empty($nameAr)) {
                    $validator->errors()->add('name_en', __('At least one of Lead Name (English) or Lead Name (Arabic) is required.'));
                }
            },
        ];
    }
}
