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
        Schema::create('rf_featured_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->constrained('rf_request_subcategories')->cascadeOnDelete();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_featured_services');
    }
};
