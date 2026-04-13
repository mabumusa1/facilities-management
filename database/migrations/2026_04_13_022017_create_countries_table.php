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
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('iso2', 2)->unique();
            $table->string('iso3', 3)->unique();
            $table->string('dial_code', 10)->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->string('capital')->nullable();
            $table->string('continent', 2)->nullable();
            $table->string('flag_emoji')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
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
