<?php

namespace App\Http\Requests\VisitorAccess;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitorInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'visitor_name' => ['required', 'string', 'max:255'],
            'visitor_purpose' => ['required', 'string', 'in:visit,delivery,service,other'],
            'expected_at' => ['required', 'date', 'after:now'],
            'visitor_phone' => ['nullable', 'string', 'max:50'],
        ];
    }
}
