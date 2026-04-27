<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_flag_overrides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_tenant_id');
            $table->string('flag_key', 100);
            $table->boolean('enabled');
            $table->timestamps();

            $table->unique(['account_tenant_id', 'flag_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_flag_overrides');
    }
};
