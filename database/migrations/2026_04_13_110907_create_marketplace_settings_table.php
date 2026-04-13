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
        Schema::create('marketplace_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            // General settings
            $table->string('marketplace_name')->nullable();
            $table->text('marketplace_description')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->boolean('is_active')->default(true);

            // Deposit settings
            $table->decimal('default_deposit_percentage', 5, 2)->default(10.00);
            $table->decimal('minimum_deposit_amount', 15, 2)->nullable();
            $table->decimal('maximum_deposit_amount', 15, 2)->nullable();
            $table->boolean('deposit_required')->default(true);
            $table->enum('deposit_refund_policy', ['full', 'partial', 'non_refundable'])->default('full');
            $table->integer('deposit_refund_days')->default(30);
            $table->text('deposit_terms')->nullable();

            // Payment terms
            $table->json('accepted_payment_methods')->nullable();
            $table->boolean('allow_installments')->default(true);
            $table->json('installment_options')->nullable();
            $table->integer('max_installment_months')->default(60);
            $table->decimal('minimum_down_payment_percentage', 5, 2)->default(20.00);
            $table->boolean('allow_mortgage')->default(true);
            $table->text('payment_terms_text')->nullable();

            // Bank account details
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_iban')->nullable();
            $table->string('bank_swift_code')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_currency', 3)->default('SAR');

            // Commission settings
            $table->decimal('agent_commission_percentage', 5, 2)->default(2.00);
            $table->decimal('platform_commission_percentage', 5, 2)->default(0.00);
            $table->boolean('commission_on_gross')->default(true);

            // Listing settings
            $table->integer('default_listing_days')->default(90);
            $table->integer('max_listing_days')->default(365);
            $table->boolean('auto_renew_listings')->default(false);
            $table->integer('featured_listing_days')->default(30);
            $table->decimal('featured_listing_fee', 15, 2)->nullable();

            // Visit settings
            $table->json('visit_available_days')->nullable();
            $table->time('visit_start_time')->nullable();
            $table->time('visit_end_time')->nullable();
            $table->integer('visit_duration_minutes')->default(60);
            $table->integer('min_visit_notice_hours')->default(24);

            // Offer settings
            $table->integer('offer_validity_days')->default(7);
            $table->boolean('allow_counter_offers')->default(true);
            $table->integer('max_negotiation_rounds')->default(5);
            $table->boolean('auto_reject_low_offers')->default(false);
            $table->decimal('min_offer_percentage', 5, 2)->nullable();

            // Notification settings
            $table->boolean('notify_on_new_inquiry')->default(true);
            $table->boolean('notify_on_new_offer')->default(true);
            $table->boolean('notify_on_visit_scheduled')->default(true);
            $table->boolean('notify_on_offer_accepted')->default(true);
            $table->json('notification_recipients')->nullable();

            // Legal/Terms
            $table->text('terms_and_conditions')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->text('cancellation_policy')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Each tenant should have only one settings record
            $table->unique('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_settings');
    }
};
