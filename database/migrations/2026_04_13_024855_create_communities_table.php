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
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('sales_commission_rate', 5, 2)->default(0.00);
            $table->decimal('rental_commission_rate', 5, 2)->default(0.00);
            $table->json('map')->nullable();
            $table->boolean('is_marketplace')->default(false);
            $table->boolean('is_buy')->default(false);
            $table->string('marketplace_type')->default('rent'); // rent, buy, both
            $table->boolean('is_off_plan_sale')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'city_id']);
            $table->index(['tenant_id', 'is_marketplace']);
        });

        // Junction table for community amenities
        Schema::create('community_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained()->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['community_id', 'amenity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_amenities');
        Schema::dropIfExists('communities');
    }
};
