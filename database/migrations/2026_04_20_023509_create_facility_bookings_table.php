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
        Schema::create('rf_facility_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('rf_facilities')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('rf_statuses');
            $table->morphs('booker');
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('number_of_guests')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_facility_bookings');
    }
};
