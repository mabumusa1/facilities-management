<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rf_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('trigger_key');
            $table->string('domain');
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->json('email_template')->nullable();
            $table->json('sms_template')->nullable();
            $table->timestamps();

            $table->unique(['account_tenant_id', 'trigger_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rf_notification_preferences');
    }
};
