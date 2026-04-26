<?php

namespace App\Http\Requests\Leasing;

use Illuminate\Foundation\Http\FormRequest;

class RejectLeaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled via policy in the controller.
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }
}
