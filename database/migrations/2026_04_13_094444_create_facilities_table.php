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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('community_id')->constrained('communities')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('facility_categories')->nullOnDelete();

            // Bilingual names
            $table->string('name_en');
            $table->string('name_ar')->nullable();

            // Facility details
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('gender', ['male', 'female', 'mixed'])->default('mixed');
            $table->enum('booking_type', ['hourly', 'daily', 'session'])->default('hourly');
            $table->json('operating_days')->nullable(); // ['sunday', 'monday', ...]
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->integer('capacity')->nullable();
            $table->decimal('price_per_hour', 10, 2)->nullable();
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->decimal('price_per_session', 10, 2)->nullable();
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('booking_duration_minutes')->nullable(); // Default session duration
            $table->integer('max_advance_booking_days')->nullable(); // How far in advance can book
            $table->text('rules_en')->nullable();
            $table->text('rules_ar')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'community_id']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
