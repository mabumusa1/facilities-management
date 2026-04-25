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
        Schema::create('rf_service_request_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained('rf_requests')->cascadeOnDelete();
            $table->morphs('sender');
            $table->text('body');
            $table->boolean('is_internal')->default(false);
            $table->unsignedBigInteger('account_tenant_id')->index();
            $table->timestamps();

            $table->index(['service_request_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_service_request_messages');
    }
};
