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
        Schema::create('marketplace_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('statuses')->restrictOnDelete();

            // Listing details
            $table->string('listing_title_en');
            $table->string('listing_title_ar')->nullable();
            $table->text('listing_description_en')->nullable();
            $table->text('listing_description_ar')->nullable();

            // Pricing
            $table->decimal('listing_price', 15, 2);
            $table->decimal('original_price', 15, 2)->nullable();
            $table->decimal('price_per_sqm', 10, 2)->nullable();
            $table->boolean('price_negotiable')->default(false);

            // Listing settings
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Agent/Broker
            $table->foreignId('listed_by')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('assigned_agent')->nullable()->constrained('contacts')->nullOnDelete();

            // Visibility
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);

            // Sale tracking
            $table->foreignId('buyer_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->timestamp('sold_at')->nullable();
            $table->decimal('sold_price', 15, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['unit_id', 'is_published']);
            $table->index('status_id');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_units');
    }
};
