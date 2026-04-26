<?php

namespace App\Http\Requests\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignServiceRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Policy check is handled by the controller's authorize() call.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'assigned_to_user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'priority' => [
                'nullable',
                'string',
                Rule::in(['low', 'medium', 'high', 'urgent']),
            ],
        ];
    }
}
