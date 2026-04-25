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
        Schema::create('rf_visitor_access_settings', function (Blueprint $table) {
            $table->id();

            // Tenant scope — BelongsToAccountTenant trait adds global scope on this column
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();

            // One settings row per community
            $table->unsignedBigInteger('community_id')->unique();
            $table->foreign('community_id')->references('id')->on('rf_communities');

            $table->boolean('require_id_verification')->default(false);
            $table->boolean('allow_walk_in')->default(true);

            // QR code validity window in minutes (default 24 hours)
            $table->unsignedSmallInteger('qr_expiry_minutes')->default(1440);

            // How many times a single invitation QR may be scanned
            $table->unsignedSmallInteger('max_uses_per_invitation')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_visitor_access_settings');
    }
};
