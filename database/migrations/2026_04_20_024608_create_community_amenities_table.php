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
        Schema::create('community_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained('rf_communities')->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained('rf_amenities')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['community_id', 'amenity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_amenities');
    }
};
