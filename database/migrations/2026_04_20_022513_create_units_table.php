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
        Schema::create('rf_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('rf_community_id')->constrained('rf_communities')->cascadeOnDelete();
            $table->foreignId('rf_building_id')->nullable()->constrained('rf_buildings')->nullOnDelete();
            $table->foreignId('category_id')->constrained('rf_unit_categories');
            $table->foreignId('type_id')->constrained('rf_unit_types');
            $table->foreignId('status_id')->constrained('rf_statuses');
            $table->foreignId('city_id')->nullable()->constrained();
            $table->foreignId('district_id')->nullable()->constrained();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->year('year_build')->nullable();
            $table->decimal('net_area', 10, 2)->nullable();
            $table->integer('floor_no')->nullable();
            $table->text('about')->nullable();
            $table->json('map')->nullable();
            $table->boolean('is_market_place')->default(false);
            $table->boolean('is_buy')->default(false);
            $table->boolean('is_off_plan_sale')->default(false);
            $table->boolean('renewal_status')->default(false);
            $table->unsignedBigInteger('marketplace_booking_unit_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_units');
    }
};
