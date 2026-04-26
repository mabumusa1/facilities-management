<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lease_quotes', function (Blueprint $table): void {
            $table->text('revision_note')->nullable()->after('version');
            $table->string('email_subject_prefix')->nullable()->after('revision_note');
            $table->text('rejection_reason')->nullable()->after('email_subject_prefix');
        });
    }

    public function down(): void
    {
        Schema::table('lease_quotes', function (Blueprint $table): void {
            $table->dropColumn(['revision_note', 'email_subject_prefix', 'rejection_reason']);
        });
    }
};
