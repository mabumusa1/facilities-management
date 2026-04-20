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
        Schema::create('admin_service_manager_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('rf_admins')->cascadeOnDelete();
            $table->foreignId('service_manager_type_id')->constrained('rf_service_manager_types')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['admin_id', 'service_manager_type_id'], 'admin_smt_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_service_manager_types');
    }
};
