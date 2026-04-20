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
        Schema::create('feature_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained('rf_features')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('rf_units')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['feature_id', 'unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_unit');
    }
};
