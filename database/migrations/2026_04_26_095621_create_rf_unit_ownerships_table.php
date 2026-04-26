<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rf_unit_ownerships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('rf_owners')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('rf_units')->cascadeOnDelete();
            $table->string('ownership_type')->default('full');
            $table->decimal('ownership_percentage', 5, 2)->default(100);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->unique(['owner_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rf_unit_ownerships');
    }
};
