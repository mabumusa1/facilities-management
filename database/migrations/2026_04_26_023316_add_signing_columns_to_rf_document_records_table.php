<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_document_records', function (Blueprint $table) {
            $table->string('signing_token', 64)->nullable()->unique()->after('status');
            $table->timestamp('sent_at')->nullable()->after('signing_token');
        });
    }

    public function down(): void
    {
        Schema::table('rf_document_records', function (Blueprint $table) {
            $table->dropColumn(['signing_token', 'sent_at']);
        });
    }
};
