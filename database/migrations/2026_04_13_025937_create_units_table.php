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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('community_id')->constrained()->cascadeOnDelete();
            $table->foreignId('building_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();

            // Basic unit information
            $table->string('name', 100);
            $table->unsignedSmallInteger('floor_no')->nullable();
            $table->decimal('net_area', 12, 2)->nullable();
            $table->year('year_built')->nullable();
            $table->decimal('market_rent', 12, 2)->nullable();
            $table->text('about')->nullable();

            // Location data
            $table->json('map')->nullable();

            // Media (stored as JSON arrays of URLs/paths)
            $table->json('photos')->nullable();

            // Marketplace flags
            $table->boolean('is_marketplace')->default(false);
            $table->boolean('is_off_plan_sale')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Indexes for efficient tenant-scoped queries
            $table->index(['tenant_id', 'community_id']);
            $table->index(['tenant_id', 'building_id']);
            $table->index(['tenant_id', 'status_id']);
            $table->index(['community_id', 'building_id']);
            $table->index(['building_id', 'status_id']);
            $table->index(['unit_category_id', 'unit_type_id']);
            $table->index('is_marketplace');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
