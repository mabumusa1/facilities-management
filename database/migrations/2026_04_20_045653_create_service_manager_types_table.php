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
        Schema::create('rf_service_manager_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rf_service_manager_types');
    }
};
