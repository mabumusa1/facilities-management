<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds bilingual label columns and a tenant-scope column to the permissions table.
 *
 * - name_en / name_ar : human-readable labels for display in the UI
 * - account_tenant_id : NULL = system-wide; non-null = tenant-custom permission
 *
 * System-wide permissions (account_tenant_id IS NULL) are seeded by RbacSeeder.
 * Tenant-custom permissions (account_tenant_id IS NOT NULL) are created by admins
 * within a tenant context and must never be deleted by the RBAC seeder.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('action');
            $table->string('name_ar')->nullable()->after('name_en');
            $table->unsignedBigInteger('account_tenant_id')->nullable()->after('name_ar');
            $table->index('account_tenant_id', 'permissions_account_tenant_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropIndex('permissions_account_tenant_id_index');
            $table->dropColumn(['name_en', 'name_ar', 'account_tenant_id']);
        });
    }
};
