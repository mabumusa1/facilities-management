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
        Schema::create('rf_unit_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('rf_units')->cascadeOnDelete();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->integer('count')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_unit_rooms');
    }
};
