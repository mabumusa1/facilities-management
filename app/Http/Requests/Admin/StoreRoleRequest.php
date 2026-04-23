<?php

namespace App\Http\Requests\Admin;

use App\Enums\RoleType;
use App\Models\Tenant;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreRoleRequest extends FormRequest
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
        $tenantId = Tenant::current()?->id;

        return [
            'name_en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name_en')
                    ->where('account_tenant_id', $tenantId),
            ],
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name_ar')
                    ->where('account_tenant_id', $tenantId),
            ],
            'type' => ['required', new Enum(RoleType::class)],
        ];
    }
}
