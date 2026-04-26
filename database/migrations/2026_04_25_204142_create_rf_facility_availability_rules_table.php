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
        Schema::create('rf_facility_availability_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('rf_facilities')->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0=Sunday … 6=Saturday
            $table->time('open_time');
            $table->time('close_time');
            $table->integer('slot_duration_minutes')->default(60);
            $table->integer('max_concurrent_bookings')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['facility_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_facility_availability_rules');
    }
};
