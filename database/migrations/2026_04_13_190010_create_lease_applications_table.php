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
        Schema::create('lease_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->comment('Multi-tenant organization');
            $table->string('application_number')->nullable();
            $table->string('status')->default('draft'); // draft, in_progress, review, approved, rejected, cancelled, on_hold

            // Applicant Information
            $table->foreignId('applicant_id')->nullable()->constrained('contacts');
            $table->string('applicant_name');
            $table->string('applicant_email')->nullable();
            $table->string('applicant_phone')->nullable();
            $table->string('applicant_type')->default('individual'); // individual, company
            $table->string('company_name')->nullable();
            $table->string('national_id')->nullable();
            $table->string('commercial_registration')->nullable();

            // Property Information
            $table->foreignId('community_id')->nullable()->constrained('communities');
            $table->foreignId('building_id')->nullable()->constrained('buildings');

            // Quote Information
            $table->decimal('quoted_rental_amount', 12, 2)->nullable();
            $table->decimal('security_deposit', 12, 2)->nullable();
            $table->date('proposed_start_date')->nullable();
            $table->date('proposed_end_date')->nullable();
            $table->integer('proposed_duration_months')->nullable();
            $table->text('special_terms')->nullable();
            $table->text('notes')->nullable();

            // Quote Document
            $table->string('quote_pdf_url')->nullable();
            $table->timestamp('quote_sent_at')->nullable();
            $table->timestamp('quote_expires_at')->nullable();

            // Review/Approval
            $table->foreignId('reviewed_by_id')->nullable()->constrained('contacts');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->string('rejection_reason')->nullable();

            // Conversion to Lease
            $table->foreignId('converted_lease_id')->nullable()->constrained('leases');
            $table->timestamp('converted_at')->nullable();

            // Tracking
            $table->foreignId('created_by_id')->nullable()->constrained('contacts');
            $table->foreignId('assigned_to_id')->nullable()->constrained('contacts');
            $table->string('source')->nullable(); // walk_in, website, referral, marketplace

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['applicant_email']);
            $table->index(['application_number']);
        });

        // Create pivot table for lease application units
        Schema::create('lease_application_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained();
            $table->decimal('proposed_rental_amount', 12, 2)->nullable();
            $table->decimal('net_area', 10, 2)->nullable();
            $table->decimal('meter_cost', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['lease_application_id', 'unit_id']);
        });

        // Create state history table for audit trail
        Schema::create('lease_application_state_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_application_id')->constrained()->onDelete('cascade');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('changed_by_id')->nullable()->constrained('contacts');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['lease_application_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_application_state_history');
        Schema::dropIfExists('lease_application_units');
        Schema::dropIfExists('lease_applications');
    }
};
