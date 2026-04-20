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
        Schema::create('rf_request_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('rf_request_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('icon_id')->nullable();
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->boolean('is_all_day')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_request_subcategories');
    }
};
