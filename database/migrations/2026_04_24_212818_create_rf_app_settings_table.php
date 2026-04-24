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
        Schema::create('rf_app_settings', function (Blueprint $table) {
            $table->id();

            // Unique per tenant — BelongsToAccountTenant trait adds global scope
            $table->unsignedBigInteger('account_tenant_id')->nullable()->unique();

            // UI customisation
            $table->json('sidebar_label_overrides')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('login_bg_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_app_settings');
    }
};
