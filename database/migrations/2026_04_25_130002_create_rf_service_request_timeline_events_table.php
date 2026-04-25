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
        Schema::create('rf_service_request_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained('rf_requests')->cascadeOnDelete();
            $table->string('event_type', 50);
            $table->nullableMorphs('actor');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['service_request_id', 'created_at']);
            $table->index(['account_tenant_id', 'event_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_service_request_timeline_events');
    }
};
