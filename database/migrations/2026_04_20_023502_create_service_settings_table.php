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
        Schema::create('rf_service_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('rf_request_categories')->cascadeOnDelete();
            $table->json('visibilities')->nullable();
            $table->json('permissions')->nullable();
            $table->string('submit_request_before_type')->nullable();
            $table->integer('submit_request_before_value')->nullable();
            $table->string('capacity_type')->nullable();
            $table->integer('capacity_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_service_settings');
    }
};
