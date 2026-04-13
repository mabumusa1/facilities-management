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
        Schema::create('marketplace_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('marketplace_unit_id')->constrained('marketplace_units')->cascadeOnDelete();
            $table->foreignId('marketplace_customer_id')->constrained('marketplace_customers')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('statuses')->restrictOnDelete();

            // Offer details
            $table->string('offer_reference')->nullable();
            $table->enum('offer_type', ['purchase', 'booking', 'lease'])->default('purchase');
            $table->decimal('offer_amount', 15, 2);
            $table->decimal('counter_offer_amount', 15, 2)->nullable();
            $table->decimal('final_amount', 15, 2)->nullable();
            $table->string('currency', 3)->default('SAR');

            // Payment terms
            $table->enum('payment_method', ['cash', 'mortgage', 'installments', 'mixed'])->nullable();
            $table->integer('installment_months')->nullable();
            $table->decimal('down_payment_percentage', 5, 2)->nullable();
            $table->decimal('down_payment_amount', 15, 2)->nullable();

            // Booking deposit
            $table->decimal('booking_deposit', 15, 2)->nullable();
            $table->timestamp('deposit_paid_at')->nullable();
            $table->timestamp('deposit_refunded_at')->nullable();
            $table->string('deposit_payment_reference')->nullable();

            // Offer conditions
            $table->text('conditions')->nullable();
            $table->text('customer_message')->nullable();
            $table->text('agent_response')->nullable();
            $table->text('rejection_reason')->nullable();

            // Timeline
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Contract details
            $table->string('contract_reference')->nullable();
            $table->timestamp('contract_signed_at')->nullable();
            $table->foreignId('contract_signed_by')->nullable()->constrained('contacts')->nullOnDelete();

            // Assignment
            $table->foreignId('assigned_agent')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('contacts')->nullOnDelete();

            // Negotiation tracking
            $table->integer('negotiation_rounds')->default(0);
            $table->boolean('is_counter_offer')->default(false);
            $table->foreignId('parent_offer_id')->nullable()->constrained('marketplace_offers')->nullOnDelete();

            // Visit reference
            $table->foreignId('marketplace_visit_id')->nullable()->constrained('marketplace_visits')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['marketplace_unit_id', 'status_id']);
            $table->index(['marketplace_customer_id', 'status_id']);
            $table->index('offer_reference');
            $table->index('contract_reference');
            $table->index('assigned_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_offers');
    }
};
