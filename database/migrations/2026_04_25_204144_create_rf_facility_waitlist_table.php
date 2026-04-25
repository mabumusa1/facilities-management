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
        Schema::create('rf_facility_waitlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('rf_facilities')->cascadeOnDelete();
            $table->foreignId('resident_id')->constrained('rf_tenants')->cascadeOnDelete();
            $table->dateTime('requested_start_at');
            $table->dateTime('requested_end_at');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('ttl_expires_at')->nullable();
            $table->timestamps();

            $table->unique(['facility_id', 'resident_id', 'requested_start_at'], 'rf_waitlist_position_unique');
            $table->index(['facility_id', 'requested_start_at', 'created_at'], 'rf_waitlist_fifo_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_facility_waitlist');
    }
};
