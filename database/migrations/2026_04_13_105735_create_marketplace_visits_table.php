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
        Schema::create('marketplace_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('marketplace_unit_id')->constrained('marketplace_units')->cascadeOnDelete();
            $table->foreignId('marketplace_customer_id')->constrained('marketplace_customers')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('statuses')->restrictOnDelete();

            // Scheduling
            $table->date('visit_date');
            $table->time('visit_time')->nullable();
            $table->time('visit_end_time')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->boolean('is_all_day')->default(false);

            // Assignment
            $table->foreignId('assigned_agent')->nullable()->constrained('contacts')->nullOnDelete();

            // Visit details
            $table->text('customer_notes')->nullable();
            $table->text('agent_notes')->nullable();
            $table->text('feedback')->nullable();
            $table->integer('interest_level')->nullable();

            // Visit outcome
            $table->enum('outcome', ['interested', 'not_interested', 'follow_up', 'offer_made', 'pending'])->nullable();

            // Confirmation and completion tracking
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('contacts')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Rescheduling
            $table->foreignId('rescheduled_from')->nullable()->constrained('marketplace_visits')->nullOnDelete();
            $table->timestamp('rescheduled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['marketplace_unit_id', 'visit_date']);
            $table->index(['marketplace_customer_id', 'visit_date']);
            $table->index('status_id');
            $table->index('assigned_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_visits');
    }
};
