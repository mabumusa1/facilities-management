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
        Schema::create('subcategory_communities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->constrained('rf_request_subcategories')->cascadeOnDelete();
            $table->foreignId('community_id')->constrained('rf_communities')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['subcategory_id', 'community_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcategory_communities');
    }
};
