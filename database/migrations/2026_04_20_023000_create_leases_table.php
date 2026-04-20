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
        Schema::create('rf_leases', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->foreignId('tenant_id')->constrained('rf_tenants');
            $table->foreignId('status_id')->constrained('rf_statuses');
            $table->foreignId('lease_unit_type_id')->constrained('rf_unit_categories');
            $table->foreignId('rental_contract_type_id')->constrained('rf_settings');
            $table->foreignId('payment_schedule_id')->constrained('rf_settings');
            $table->unsignedBigInteger('created_by_id');
            $table->unsignedBigInteger('deal_owner_id')->nullable();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('handover_date');
            $table->date('actual_end_at')->nullable();
            $table->string('tenant_type');
            $table->string('rental_type');
            $table->decimal('rental_total_amount', 12, 2);
            $table->decimal('security_deposit_amount', 12, 2)->nullable();
            $table->date('security_deposit_due_date')->nullable();
            $table->string('lease_escalations_type')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->boolean('is_terms')->default(false);
            $table->boolean('is_sub_lease')->default(false);
            $table->foreignId('parent_lease_id')->nullable()->constrained('rf_leases')->nullOnDelete();
            $table->string('legal_representative')->nullable();
            $table->string('fit_out_status')->nullable();
            $table->integer('free_period')->default(0);
            $table->integer('number_of_years')->nullable();
            $table->integer('number_of_months')->nullable();
            $table->integer('number_of_days')->nullable();
            $table->boolean('is_renew')->default(false);
            $table->boolean('is_move_out')->default(false);
            $table->boolean('is_old')->default(false);
            $table->string('pdf_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_leases');
    }
};
