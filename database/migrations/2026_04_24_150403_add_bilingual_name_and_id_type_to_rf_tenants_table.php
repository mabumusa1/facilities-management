<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rf_tenants', function (Blueprint $table) {
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('first_name_ar')->nullable()->after('first_name');
            $table->string('last_name_ar')->nullable()->after('last_name');
            $table->string('id_type')->nullable()->after('national_id');

            // Composite unique index for phone-based duplicate detection within a tenant
            $table->unique(['account_tenant_id', 'national_phone_number'], 'rf_tenants_tenant_phone_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_tenants', function (Blueprint $table) {
            $table->dropUnique('rf_tenants_tenant_phone_unique');
            $table->dropColumn(['first_name_ar', 'last_name_ar', 'id_type']);
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
        });
    }
};
