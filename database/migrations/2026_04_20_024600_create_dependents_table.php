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
        Schema::create('rf_dependents', function (Blueprint $table) {
            $table->id();
            $table->morphs('dependable');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_country_code')->nullable();
            $table->string('email')->nullable();
            $table->string('national_id')->nullable();
            $table->string('gender')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('relationship')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_dependents');
    }
};
