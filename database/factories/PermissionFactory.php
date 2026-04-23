<?php

namespace Database\Factories;

use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $subject = fake()->randomElement(PermissionSubject::cases());
        $action = fake()->randomElement(PermissionAction::cases());

        return [
            'name' => $subject->value.'.'.$action->value,
            'guard_name' => 'web',
            'subject' => $subject,
            'action' => $action,
        ];
    }
}
