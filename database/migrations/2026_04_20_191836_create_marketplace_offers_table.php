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
        Schema::create('rf_marketplace_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('rf_units')->cascadeOnDelete();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('discount_type')->default('percentage');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_marketplace_offers');
    }
};
