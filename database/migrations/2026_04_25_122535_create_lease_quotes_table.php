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
        Schema::create('lease_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_tenant_id')->constrained('tenants')->cascadeOnDelete();

            // Auto-generated Q-YYYYMMDD-{seq}; nullable until explicitly assigned.
            $table->string('quote_number')->nullable()->unique();

            $table->foreignId('unit_id')->constrained('rf_units')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('rf_tenants')->cascadeOnDelete();

            // FK to rf_contract_types (story #226). Nullable so this migration
            // is independent of the contract-types UI story shipping order.
            $table->foreignId('contract_type_id')->nullable()->constrained('rf_contract_types')->nullOnDelete();

            $table->foreignId('status_id')->constrained('rf_statuses')->restrictOnDelete();
            $table->unsignedInteger('duration_months');
            $table->date('start_date');
            $table->decimal('rent_amount', 15, 2);

            // Setting row of type=payment_frequency (IDs 10,11 in rf_settings).
            $table->foreignId('payment_frequency_id')->constrained('rf_settings')->restrictOnDelete();

            $table->decimal('security_deposit', 15, 2)->default(0);

            // [{label:{en,ar}, amount:float}, ...] — EN+AR pairs per Designer spec.
            $table->json('additional_charges')->nullable();

            // {en: string, ar: string}
            $table->json('special_conditions')->nullable();

            $table->dateTime('valid_until');

            // Revision chain: version increments on revise (story #171).
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('parent_quote_id')->nullable()->constrained('lease_quotes')->nullOnDelete();

            // Attribution for marketplace origin (story #276). rf_marketplace_units exists.
            $table->foreignId('marketplace_unit_id')->nullable()->constrained('rf_marketplace_units')->nullOnDelete();

            $table->foreignId('created_by_id')->constrained('rf_admins')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_quotes');
    }
};
