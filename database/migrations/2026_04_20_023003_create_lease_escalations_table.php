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
        Schema::create('rf_lease_escalations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('rf_leases')->cascadeOnDelete();
            $table->integer('year');
            $table->string('type');
            $table->decimal('value', 12, 2);
            $table->decimal('new_amount', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_lease_escalations');
    }
};
