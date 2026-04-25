<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Story #148 lets the operator confirm and create a record even when a
     * phone-number duplicate is detected. The strict unique composite index
     * added by the #147 migration would block that path at the DB layer, so
     * replace it with a regular index. Application-layer guards in
     * StoreResidentRequest + the duplicate-check endpoint enforce uniqueness
     * unless `force_create` is supplied.
     */
    public function up(): void
    {
        Schema::table('rf_tenants', function (Blueprint $table): void {
            $table->dropUnique('rf_tenants_tenant_phone_unique');
            $table->index(['account_tenant_id', 'national_phone_number'], 'rf_tenants_tenant_phone_index');
        });

        Schema::table('rf_owners', function (Blueprint $table): void {
            $table->dropUnique('rf_owners_tenant_phone_unique');
            $table->index(['account_tenant_id', 'national_phone_number'], 'rf_owners_tenant_phone_index');
        });
    }

    public function down(): void
    {
        Schema::table('rf_tenants', function (Blueprint $table): void {
            $table->dropIndex('rf_tenants_tenant_phone_index');
            $table->unique(['account_tenant_id', 'national_phone_number'], 'rf_tenants_tenant_phone_unique');
        });

        Schema::table('rf_owners', function (Blueprint $table): void {
            $table->dropIndex('rf_owners_tenant_phone_index');
            $table->unique(['account_tenant_id', 'national_phone_number'], 'rf_owners_tenant_phone_unique');
        });
    }
};
