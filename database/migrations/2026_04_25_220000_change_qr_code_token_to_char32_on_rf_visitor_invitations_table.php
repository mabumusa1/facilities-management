<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change qr_code_token from uuid (char 36) to char(32) to match the
     * bin2hex(random_bytes(16)) token format — a 32-character hex string.
     */
    public function up(): void
    {
        Schema::table('rf_visitor_invitations', function (Blueprint $table) {
            $table->char('qr_code_token', 32)->change();
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('rf_visitor_invitations', function (Blueprint $table) {
            $table->uuid('qr_code_token')->change();
        });
    }
};
