<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('move_outs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lease_id')->constrained('rf_leases')->cascadeOnDelete();
            $table->date('move_out_date');
            $table->string('reason');
            $table->foreignId('status_id')->constrained('rf_statuses');
            $table->foreignId('initiated_by')->constrained('users');
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('move_outs');
    }
};
