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
        Schema::create('rf_buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('rf_community_id')->constrained('rf_communities')->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained();
            $table->foreignId('district_id')->nullable()->constrained();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->integer('no_floors')->nullable()->default(0);
            $table->year('year_build')->nullable();
            $table->json('map')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_buildings');
    }
};
