<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_flag_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_tenant_id');
            $table->unsignedBigInteger('user_id');
            $table->string('flag_key', 100);
            $table->string('action', 20);
            $table->timestamp('created_at')->nullable();

            $table->index('account_tenant_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_flag_audit_logs');
    }
};
