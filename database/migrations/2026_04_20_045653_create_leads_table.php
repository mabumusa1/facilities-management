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
        Schema::create('rf_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('source_id')->nullable()->constrained('rf_lead_sources')->nullOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('rf_statuses')->nullOnDelete();
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->unsignedBigInteger('lead_owner_id')->nullable();
            $table->string('interested')->nullable();
            $table->timestamp('lead_last_contact_at')->nullable();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rf_leads');
    }
};
