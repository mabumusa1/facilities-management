<?php

namespace App\Http\Requests\Leasing;

use App\Enums\LeaseNoticeType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeaseNoticeRequest extends FormRequest
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
            'type' => ['required', Rule::enum(LeaseNoticeType::class)],
            'subject_en' => ['required', 'string', 'max:255'],
            'body_en' => ['required', 'string'],
            'subject_ar' => ['required', 'string', 'max:255'],
            'body_ar' => ['required', 'string'],
        ];
    }
}
