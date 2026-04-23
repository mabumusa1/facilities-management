<?php

namespace App\Models;

use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    /** @use HasFactory<PermissionFactory> */
    use HasFactory;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'subject' => PermissionSubject::class,
            'action' => PermissionAction::class,
        ]);
    }
}
