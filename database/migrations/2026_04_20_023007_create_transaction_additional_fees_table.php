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
        Schema::create('rf_transaction_additional_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('rf_transactions')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_transaction_additional_fees');
    }
};
