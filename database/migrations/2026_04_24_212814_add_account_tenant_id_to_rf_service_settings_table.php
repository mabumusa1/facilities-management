<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fixes pre-existing tenant-boundary leak: rf_service_settings had no
     * account_tenant_id column, so all tenants could see each other's service
     * settings. This migration adds the FK column, an index, and a composite
     * unique constraint on (account_tenant_id, category_id) to enforce
     * one-setting-row per category per tenant.
     *
     * Existing rows will have account_tenant_id = null. Super-admin context
     * (no Tenant::current()) will still see those rows; tenant-scoped requests
     * will not (BelongsToAccountTenant global scope filters by tenant id).
     * Run `php artisan settings:seed-tenants` to backfill existing rows.
     */
    public function up(): void
    {
        Schema::table('rf_service_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index()->after('category_id');

            // One service-setting row per category per tenant
            $table->unique(['account_tenant_id', 'category_id'], 'rf_service_settings_tenant_category_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_service_settings', function (Blueprint $table) {
            $table->dropUnique('rf_service_settings_tenant_category_unique');
            $table->dropIndex(['account_tenant_id']);
            $table->dropColumn('account_tenant_id');
        });
    }
};
