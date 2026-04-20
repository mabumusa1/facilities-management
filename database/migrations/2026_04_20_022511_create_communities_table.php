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
        Schema::create('rf_communities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('country_id')->constrained();
            $table->foreignId('currency_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->foreignId('district_id')->constrained();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->decimal('sales_commission_rate', 10, 2)->nullable();
            $table->decimal('rental_commission_rate', 10, 2)->nullable();
            $table->json('map')->nullable();
            $table->boolean('is_market_place')->default(false);
            $table->boolean('is_buy')->default(false);
            $table->string('community_marketplace_type')->nullable();
            $table->boolean('is_off_plan_sale')->default(false);
            $table->boolean('is_selected_property')->default(false);
            $table->integer('count_selected_property')->default(0);
            $table->decimal('total_income', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_communities');
    }
};
