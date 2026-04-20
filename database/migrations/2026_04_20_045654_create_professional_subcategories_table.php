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
        Schema::create('professional_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained('rf_professionals')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->constrained('rf_request_subcategories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['professional_id', 'subcategory_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professional_subcategories');
    }
};
