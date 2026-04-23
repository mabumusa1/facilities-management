<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the Spatie `roles` table with bilingual labels, a type discriminator,
 * and a tenant scope column.
 *
 * BREAKING: The default Spatie unique index on (name, guard_name) is replaced
 * with a tenant-scoped unique index on (account_tenant_id, name, guard_name)
 * to allow two different tenants to each have a role named e.g. "admins".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_ar');
            $table->string('type')->nullable()->after('name_en');
            $table->unsignedBigInteger('account_tenant_id')->nullable()->after('type');
            $table->index('account_tenant_id', 'roles_account_tenant_id_index');

            // Replace the default Spatie unique index with a tenant-scoped one.
            $table->dropUnique(['name', 'guard_name']);
            $table->unique(['account_tenant_id', 'name', 'guard_name'], 'roles_tenant_name_guard_unique');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_tenant_name_guard_unique');
            $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
            $table->dropIndex('roles_account_tenant_id_index');
            $table->dropColumn(['name_ar', 'name_en', 'type', 'account_tenant_id']);
        });
    }
};
