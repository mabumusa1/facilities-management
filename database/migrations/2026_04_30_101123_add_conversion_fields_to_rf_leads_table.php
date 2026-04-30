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
        Schema::table('rf_leads', function (Blueprint $table): void {
            $table->nullableMorphs('converted_contact');
            $table->timestamp('converted_at')->nullable()->after('converted_contact_type');

            // Composite indexes for fast dedup queries
            $table->index(['account_tenant_id', 'converted_contact_id', 'converted_contact_type'], 'rf_leads_tenant_converted_idx');
        });

        // Add indexes on rf_owners and rf_residents for dedup lookups
        Schema::table('rf_owners', function (Blueprint $table): void {
            $table->index(['account_tenant_id', 'email'], 'rf_owners_tenant_email_idx');
            $table->index(['account_tenant_id', 'phone_number'], 'rf_owners_tenant_phone_idx');
        });

        Schema::table('rf_tenants', function (Blueprint $table): void {
            $table->index(['account_tenant_id', 'email'], 'rf_tenants_tenant_email_idx');
            $table->index(['account_tenant_id', 'phone_number'], 'rf_tenants_tenant_phone_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rf_tenants', function (Blueprint $table): void {
            $table->dropIndex('rf_tenants_tenant_email_idx');
            $table->dropIndex('rf_tenants_tenant_phone_idx');
        });

        Schema::table('rf_owners', function (Blueprint $table): void {
            $table->dropIndex('rf_owners_tenant_email_idx');
            $table->dropIndex('rf_owners_tenant_phone_idx');
        });

        Schema::table('rf_leads', function (Blueprint $table): void {
            $table->dropIndex('rf_leads_tenant_converted_idx');
            $table->dropMorphs('converted_contact');
            $table->dropColumn('converted_at');
        });
    }
};
