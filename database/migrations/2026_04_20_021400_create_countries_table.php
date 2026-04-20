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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('iso2', 2);
            $table->string('iso3', 3);
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('dial')->nullable();
            $table->string('currency')->nullable();
            $table->string('capital')->nullable();
            $table->string('continent', 2)->nullable();
            $table->string('unicode')->nullable();
            $table->string('excel')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
