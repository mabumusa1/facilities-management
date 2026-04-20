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
        Schema::create('lease_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('rf_leases')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('rf_units')->cascadeOnDelete();
            $table->string('rental_annual_type')->nullable();
            $table->decimal('annual_rental_amount', 12, 2)->nullable();
            $table->decimal('net_area', 10, 2)->nullable();
            $table->decimal('meter_cost', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['lease_id', 'unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_units');
    }
};
