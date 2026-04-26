<?php

namespace App\Http\Requests\Services;

use App\Models\ServiceSubcategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResidentServiceRequestRequest extends FormRequest
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
            'service_category_id' => ['required', 'integer', 'exists:service_categories,id'],
            'service_subcategory_id' => [
                'nullable',
                'integer',
                Rule::exists('service_subcategories', 'id')->where('service_category_id', $this->input('service_category_id')),
            ],
            'unit_id' => ['required', 'integer', 'exists:rf_units,id'],
            'community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'room_location' => ['nullable', 'string', 'max:100'],
            'urgency' => ['required', Rule::in(['normal', 'urgent'])],
            'description' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }
}
