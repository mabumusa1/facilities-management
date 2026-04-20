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
        Schema::create('rf_marketplace_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('rf_units')->cascadeOnDelete();
            $table->string('listing_type');
            $table->decimal('price', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_marketplace_units');
    }
};
