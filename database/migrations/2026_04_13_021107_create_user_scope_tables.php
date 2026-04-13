<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates junction tables for user scope access control:
     * - user_communities: Links users to specific communities they can access
     * - user_buildings: Links users to specific buildings they can access
     */
    public function up(): void
    {
        // User-Community junction table for scoped access
        Schema::create('user_communities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('community_id');
            $table->timestamps();

            $table->unique(['user_id', 'community_id']);
            $table->index('community_id');
        });

        // User-Building junction table for scoped access
        Schema::create('user_buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('building_id');
            $table->timestamps();

            $table->unique(['user_id', 'building_id']);
            $table->index('building_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_buildings');
        Schema::dropIfExists('user_communities');
    }
};
