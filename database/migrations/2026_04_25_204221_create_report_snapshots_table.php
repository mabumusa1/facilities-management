<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_snapshots', function (Blueprint $table): void {
            $table->id();

            // Tenant isolation — every row belongs to exactly one tenant
            $table->unsignedBigInteger('account_tenant_id');
            $table->foreign('account_tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete();

            // The type of report this snapshot represents
            $table->string('report_type')->index();

            // Report period window (both nullable — live reports may omit these)
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            // When the payload was computed; null until status=ready
            $table->timestamp('generated_at')->nullable();

            // JSONB payload — the cached computed report output
            $table->jsonb('payload')->nullable();

            // Lifecycle status: pending → ready | failed
            $table->string('status')->default('pending');

            // Who requested the snapshot generation
            $table->foreignId('requested_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Arbitrary filter params used to generate this snapshot
            $table->jsonb('filters')->nullable();

            // Populated when status=failed
            $table->text('error_message')->nullable();

            $table->timestamps();

            // Composite indexes for the expected query patterns
            $table->index(['account_tenant_id', 'report_type', 'period_start']);
            $table->index(['account_tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_snapshots');
    }
};
