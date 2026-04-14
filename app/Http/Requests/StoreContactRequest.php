<?php

namespace App\Http\Requests;

use App\Enums\ManagerRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'contact_type' => ['required', Rule::in(['owner', 'tenant', 'admin', 'professional'])],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', 'unique:contacts,email'],
            'phone_number' => ['required', 'string', 'max:20'],
            'national_phone_number' => ['nullable', 'string', 'max:20'],
            'phone_country_code' => ['required', 'string', 'size:2'],
            'role' => [Rule::requiredIf($this->input('contact_type') === 'admin'), 'nullable', Rule::in($this->managerRoleKeys())],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'national_id' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'georgian_birthdate' => ['nullable', 'date'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string>
     */
    private function managerRoleKeys(): array
    {
        return array_map(static fn (ManagerRole $role): string => $role->key(), ManagerRole::cases());
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->phone_country_code)) {
            $this->merge([
                'phone_country_code' => strtoupper($this->phone_country_code),
            ]);
        }
    }
}
