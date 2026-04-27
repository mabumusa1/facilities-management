<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->after('phone_number');
            $table->string('invitation_token', 64)->nullable()->after('status');
            $table->timestamp('invitation_expires_at')->nullable()->after('invitation_token');

            $table->index('status', 'users_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_status_index');
            $table->dropColumn(['status', 'invitation_token', 'invitation_expires_at']);
        });
    }
};
