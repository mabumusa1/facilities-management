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
        Schema::create('subcategory_buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->constrained('rf_request_subcategories')->cascadeOnDelete();
            $table->foreignId('building_id')->constrained('rf_buildings')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['subcategory_id', 'building_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcategory_buildings');
    }
};
