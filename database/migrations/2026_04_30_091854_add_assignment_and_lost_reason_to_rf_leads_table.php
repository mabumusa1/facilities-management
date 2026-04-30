<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_leads', function (Blueprint $table): void {
            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->after('lead_owner_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->text('lost_reason')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('rf_leads', function (Blueprint $table): void {
            $table->dropForeign(['assigned_to_user_id']);
            $table->dropColumn(['assigned_to_user_id', 'lost_reason']);
        });
    }
};
