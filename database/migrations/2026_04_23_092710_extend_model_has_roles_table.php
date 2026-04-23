<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the Spatie `model_has_roles` pivot table with optional scope columns
 * that allow a role assignment to be further scoped to a community, building,
 * or service type (used by manager-class roles).
 *
 * Hard FK constraints are intentionally omitted: the table has a composite PK
 * and uses a polymorphic morph key, making named FK constraints impractical.
 * Index-only is sufficient for query performance.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('community_id')->nullable()->after('model_id');
            $table->unsignedBigInteger('building_id')->nullable()->after('community_id');
            $table->unsignedBigInteger('service_type_id')->nullable()->after('building_id');

            $table->index('community_id', 'model_has_roles_community_id_index');
            $table->index('building_id', 'model_has_roles_building_id_index');
            $table->index('service_type_id', 'model_has_roles_service_type_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropIndex('model_has_roles_community_id_index');
            $table->dropIndex('model_has_roles_building_id_index');
            $table->dropIndex('model_has_roles_service_type_id_index');
            $table->dropColumn(['community_id', 'building_id', 'service_type_id']);
        });
    }
};
