<?php

namespace App\Http\Requests\Admin;

use App\Enums\RolesEnum;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRoleAssignmentRequest extends FormRequest
{
    private ?string $resolvedScopeLevel = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Gate check is done in the controller.
    }

    /**
     * Prepare the request for validation by resolving scope level.
     */
    protected function prepareForValidation(): void
    {
        $roleId = $this->input('role_id');

        if ($roleId) {
            /** @var Role|null $role */
            $role = Role::find($roleId);
            $this->resolvedScopeLevel = $role
                ? (RolesEnum::tryFrom($role->name)?->scopeLevel() ?? 'none')
                : 'none';
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tenant = Tenant::current();
        $scopeLevel = $this->resolvedScopeLevel ?? 'none';

        $communityRule = match ($scopeLevel) {
            'manager', 'serviceManager' => ['required', 'integer', Rule::exists('rf_communities', 'id')->where('account_tenant_id', $tenant?->id)],
            'none' => [Rule::prohibitedIf(true)],
            default => ['nullable', 'integer'],
        };

        $buildingRule = match ($scopeLevel) {
            'manager', 'serviceManager' => ['nullable', 'integer', Rule::exists('rf_buildings', 'id')->where('account_tenant_id', $tenant?->id)],
            'none' => [Rule::prohibitedIf(true)],
            default => ['nullable', 'integer'],
        };

        $serviceTypeRule = match ($scopeLevel) {
            'serviceManager' => ['required', 'integer', Rule::exists('rf_service_manager_types', 'id')],
            'none' => [Rule::prohibitedIf(true)],
            default => ['nullable', 'integer'],
        };

        return [
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            'community_id' => $communityRule,
            'building_id' => $buildingRule,
            'service_type_id' => $serviceTypeRule,
        ];
    }
}
