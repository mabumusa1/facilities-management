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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            // Contact type: owner, tenant, admin, professional
            $table->enum('contact_type', ['owner', 'tenant', 'admin', 'professional']);

            // Basic information
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->unique();
            $table->string('image')->nullable();

            // Personal information
            $table->date('georgian_birthdate')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('national_id', 50)->nullable();
            $table->string('nationality', 50)->nullable();

            // Phone information
            $table->string('phone_number', 20); // Full international format: +966500000002
            $table->string('national_phone_number', 20); // Without country code: 0500000002
            $table->string('phone_country_code', 2); // ISO 3166-1 alpha-2: SA

            // Status and activity
            $table->boolean('active')->default(true);
            $table->timestamp('account_creation_date')->useCurrent();
            $table->timestamp('last_active')->nullable();

            // Tenant-specific fields
            $table->string('source')->nullable(); // How the tenant was added
            $table->boolean('accepted_invite')->default(false); // Whether tenant accepted app invitation

            // Additional fields (relationship with other contacts)
            $table->string('relation')->nullable();
            $table->string('relation_key')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tenant_id');
            $table->index('contact_type');
            $table->index('email');
            $table->index('active');
            $table->index(['tenant_id', 'contact_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
