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
        Schema::create('lease_notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('rf_leases')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('rf_tenants')->cascadeOnDelete();
            $table->foreignId('sent_by')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->string('subject_en');
            $table->text('body_en');
            $table->string('subject_ar');
            $table->text('body_ar');
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('account_tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_notices');
    }
};
