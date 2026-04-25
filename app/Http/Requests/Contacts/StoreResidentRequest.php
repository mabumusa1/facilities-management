<?php

namespace App\Http\Requests\Contacts;

use App\Enums\IdType;
use App\Models\Resident;
use App\Support\PhoneNumberNormalizer;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Resident::class) ?? false;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['nullable', 'required_without:first_name_ar', 'string', 'max:255'],
            'last_name' => ['nullable', 'required_without:last_name_ar', 'string', 'max:255'],
            'first_name_ar' => ['nullable', 'string', 'max:255'],
            'last_name_ar' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_country_code' => ['required', 'string', 'max:5'],
            'phone_number' => ['required', 'string', 'min:5', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'id_type' => ['nullable', Rule::enum(IdType::class)],
            'nationality_id' => ['nullable', 'integer', 'exists:countries,id'],
            'gender' => ['nullable', 'in:male,female'],
            'georgian_birthdate' => ['nullable', 'date'],
            'force_create' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone_number.regex' => __('Enter a valid phone number.'),
            'first_name.required_without' => __('First name is required.'),
            'last_name.required_without' => __('Last name is required.'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->has('phone_number') || $validator->errors()->has('phone_country_code')) {
                return;
            }

            if ($this->boolean('force_create')) {
                return;
            }

            $normalized = PhoneNumberNormalizer::normalize(
                (string) $this->input('phone_number'),
                (string) $this->input('phone_country_code'),
            );

            if ($normalized === null) {
                return;
            }

            $exists = Resident::query()
                ->where('national_phone_number', $normalized)
                ->exists();

            if ($exists) {
                $validator->errors()->add(
                    'phone_number',
                    __('A resident with this phone number already exists.'),
                );
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function persistedAttributes(): array
    {
        $data = $this->validated();
        unset($data['force_create']);

        $normalized = PhoneNumberNormalizer::normalize(
            (string) ($data['phone_number'] ?? ''),
            (string) ($data['phone_country_code'] ?? ''),
        );

        if ($normalized !== null) {
            $data['national_phone_number'] = $normalized;
        }

        return $data;
    }
}
