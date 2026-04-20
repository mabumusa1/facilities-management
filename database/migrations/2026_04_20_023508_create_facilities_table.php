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
        Schema::create('rf_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('rf_facility_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->integer('capacity')->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->decimal('booking_fee', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_approval')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_facilities');
    }
};
