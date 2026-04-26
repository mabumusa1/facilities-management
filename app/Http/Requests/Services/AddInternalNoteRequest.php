<?php

namespace App\Http\Requests\Services;

use Illuminate\Foundation\Http\FormRequest;

class AddInternalNoteRequest extends FormRequest
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
            'body' => ['required', 'string', 'max:2000'],
        ];
    }
}
