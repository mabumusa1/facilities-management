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
        Schema::create('marketplace_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();

            // Personal details (used if no contact linked)
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('national_id')->nullable();

            // Customer type and status
            $table->enum('customer_type', ['buyer', 'renter', 'investor'])->default('buyer');
            $table->enum('status', ['lead', 'active', 'qualified', 'negotiating', 'converted', 'inactive'])->default('lead');

            // Budget preferences
            $table->decimal('budget_min', 15, 2)->nullable();
            $table->decimal('budget_max', 15, 2)->nullable();
            $table->string('preferred_payment_method')->nullable();

            // Unit preferences
            $table->json('preferred_unit_types')->nullable();
            $table->json('preferred_locations')->nullable();
            $table->integer('preferred_bedrooms_min')->nullable();
            $table->integer('preferred_bedrooms_max')->nullable();
            $table->decimal('preferred_area_min', 10, 2)->nullable();
            $table->decimal('preferred_area_max', 10, 2)->nullable();

            // Lead tracking
            $table->string('source')->nullable();
            $table->string('campaign')->nullable();
            $table->integer('lead_score')->default(0);
            $table->text('notes')->nullable();

            // Assignment
            $table->foreignId('assigned_agent')->nullable()->constrained('contacts')->nullOnDelete();

            // Conversion tracking
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('converted_unit_id')->nullable()->constrained('marketplace_units')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('email');
            $table->index('status');
            $table->index('customer_type');
            $table->index('assigned_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_customers');
    }
};
