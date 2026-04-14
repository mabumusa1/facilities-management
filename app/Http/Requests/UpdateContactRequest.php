<?php

namespace App\Http\Requests;

use App\Enums\ManagerRole;
use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
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
        $contact = $this->route('contact');
        $contactId = $contact instanceof Contact ? $contact->id : $contact;
        $effectiveContactType = $this->input('contact_type');
        if ($effectiveContactType === null && $contact instanceof Contact) {
            $effectiveContactType = $contact->contact_type;
        }

        return [
            'contact_type' => ['sometimes', Rule::in(['owner', 'tenant', 'admin', 'professional'])],
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('contacts', 'email')->ignore($contactId)],
            'phone_number' => ['required', 'string', 'max:20'],
            'national_phone_number' => ['nullable', 'string', 'max:20'],
            'phone_country_code' => ['required', 'string', 'size:2'],
            'role' => [Rule::requiredIf($effectiveContactType === 'admin'), 'nullable', Rule::in($this->managerRoleKeys())],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'national_id' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'georgian_birthdate' => ['nullable', 'date'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $contactType = $this->input('contact_type');
        if ($contactType === null && $this->route('contact') instanceof Contact) {
            $contactType = $this->route('contact')->contact_type;
        }

        $role = $this->input('role');
        if ($role === null && $contactType === 'admin' && $this->route('contact') instanceof Contact) {
            $role = $this->route('contact')->role;
        }

        $payload = [];
        if ($contactType !== null) {
            $payload['contact_type'] = $contactType;
        }

        if ($role !== null) {
            $payload['role'] = $role;
        }

        if (is_string($this->phone_country_code)) {
            $payload['phone_country_code'] = strtoupper($this->phone_country_code);
        }

        if ($payload !== []) {
            $this->merge($payload);
        }
    }

    /**
     * @return array<string>
     */
    private function managerRoleKeys(): array
    {
        return array_map(static fn (ManagerRole $role): string => $role->key(), ManagerRole::cases());
    }
}
