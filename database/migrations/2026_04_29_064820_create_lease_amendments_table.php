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
        Schema::create('lease_amendments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('rf_leases')->cascadeOnDelete();
            $table->foreignId('amended_by')->constrained('users');
            $table->text('reason');
            $table->json('changes');
            $table->unsignedBigInteger('addendum_media_id')->nullable();
            $table->unsignedInteger('amendment_number');
            $table->timestamps();

            $table->index(['lease_id', 'amendment_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_amendments');
    }
};
