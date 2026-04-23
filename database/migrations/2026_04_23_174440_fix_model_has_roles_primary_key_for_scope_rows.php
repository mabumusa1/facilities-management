<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The original model_has_roles table has a composite PK on (role_id, model_id, model_type).
 * Story #113 (manager scope) requires that a user can hold the same role multiple times
 * with different scope column values (e.g., community_id A and community_id B both as manager).
 *
 * This migration drops the original PK, adds a surrogate auto-increment id column as PK,
 * and adds a unique index on (role, model, scope tuple with COALESCE for NULLs) to prevent
 * exact duplicate assignments.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing primary key constraint.
        DB::statement('ALTER TABLE model_has_roles DROP CONSTRAINT model_has_roles_pkey');

        // Add a surrogate auto-increment primary key.
        DB::statement('ALTER TABLE model_has_roles ADD COLUMN id BIGSERIAL PRIMARY KEY');

        // Add a unique index to prevent exact duplicate scope assignments.
        // COALESCE to 0 ensures NULL+NULL is treated as the same slot.
        DB::statement(
            'CREATE UNIQUE INDEX model_has_roles_scope_unique
             ON model_has_roles (role_id, model_id, model_type,
                 COALESCE(community_id, 0),
                 COALESCE(building_id, 0),
                 COALESCE(service_type_id, 0))'
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS model_has_roles_scope_unique');
        DB::statement('ALTER TABLE model_has_roles DROP COLUMN IF EXISTS id');
        DB::statement(
            'ALTER TABLE model_has_roles ADD CONSTRAINT model_has_roles_pkey
             PRIMARY KEY (role_id, model_id, model_type)'
        );
    }
};
