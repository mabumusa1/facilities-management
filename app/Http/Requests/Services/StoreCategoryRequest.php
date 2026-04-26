<?php

namespace App\Http\Requests\Services;

use App\Models\ServiceCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', ServiceCategory::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'icon' => ['required', 'string', 'max:10'],
            'response_sla_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'resolution_sla_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'default_assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'require_completion_photo' => ['boolean'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'community_ids' => ['present', 'array', 'min:1'],
            'community_ids.*' => ['integer', 'exists:rf_communities,id'],
        ];
    }
}
