<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rf_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('community_id')->nullable()->constrained('rf_communities')->nullOnDelete();
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number', 30);
            $table->string('iban', 34)->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rf_bank_accounts');
    }
};
