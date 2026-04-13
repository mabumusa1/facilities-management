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
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('statuses');
            $table->foreignId('created_by_id')->nullable()->constrained('contacts');
            $table->foreignId('deal_owner_id')->nullable()->constrained('contacts');
            $table->foreignId('community_id')->nullable()->constrained('communities');
            $table->foreignId('building_id')->nullable()->constrained('buildings');
            $table->unsignedBigInteger('lease_unit_type_id')->nullable();
            $table->unsignedBigInteger('rental_contract_type_id')->nullable();
            $table->unsignedBigInteger('payment_schedule_id')->nullable();
            $table->unsignedBigInteger('parent_lease_id')->nullable();

            // Contract details
            $table->string('contract_number', 100)->unique();
            $table->enum('tenant_type', ['individual', 'corporate'])->default('individual');
            $table->enum('rental_type', ['summary', 'detailed'])->default('detailed');
            $table->decimal('rental_total_amount', 15, 2)->default(0);
            $table->decimal('security_deposit_amount', 15, 2)->nullable();
            $table->date('security_deposit_due_date')->nullable();
            $table->text('legal_representative')->nullable();
            $table->string('fit_out_status', 100)->nullable();

            // Dates
            $table->date('start_date');
            $table->date('end_date');
            $table->date('handover_date')->nullable();
            $table->date('actual_end_at')->nullable();

            // Duration
            $table->integer('free_period')->default(0);
            $table->integer('number_of_years')->default(0);
            $table->integer('number_of_months')->default(0);
            $table->integer('number_of_days')->nullable();

            // Escalations
            $table->enum('lease_escalations_type', ['fixed', 'percentage'])->default('fixed');
            $table->json('lease_escalations')->nullable();
            $table->json('additional_fees_lease')->nullable();

            // Terms
            $table->text('terms_conditions')->nullable();
            $table->boolean('is_terms')->default(false);

            // Status flags
            $table->boolean('is_sub_lease')->default(false);
            $table->boolean('is_renew')->default(false);
            $table->boolean('is_move_out')->default(false);
            $table->boolean('is_old')->default(false);

            // File storage
            $table->string('pdf_url')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('contract_number');
            $table->index('tenant_id');
            $table->index('status_id');
            $table->index(['start_date', 'end_date']);
            $table->index('parent_lease_id');
        });

        // Pivot table for lease-unit many-to-many relationship
        Schema::create('lease_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->enum('rental_annual_type', ['total', 'per_meter'])->default('total');
            $table->decimal('annual_rental_amount', 15, 2)->default(0);
            $table->decimal('net_area', 10, 2)->nullable();
            $table->decimal('meter_cost', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['lease_id', 'unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
