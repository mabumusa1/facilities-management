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
        Schema::create('rf_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->unique()->constrained('rf_transactions')->cascadeOnDelete();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->string('status', 30)->default('generated');
            $table->string('pdf_path', 500)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('sent_to_name')->nullable();
            $table->string('sent_to_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_receipts');
    }
};
