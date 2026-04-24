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
        Schema::create('rf_contract_types', function (Blueprint $table) {
            $table->id();

            // Tenant scope — BelongsToAccountTenant trait adds global scope on this column
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();

            // Bilingual name
            $table->string('name_en', 255);
            $table->string('name_ar', 255)->nullable();

            // Leasing defaults (mirrors LeaseEscalationType enum values for escalation_type)
            $table->unsignedSmallInteger('default_payment_terms_days')->nullable();
            $table->string('default_escalation_type', 50)->nullable();

            // Visibility and ordering
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            // One name per tenant
            $table->unique(['account_tenant_id', 'name_en'], 'rf_contract_types_tenant_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_contract_types');
    }
};
