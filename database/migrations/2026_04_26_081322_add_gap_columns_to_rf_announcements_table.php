<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rf_announcements', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->nullable()->after('content');
            $table->string('audience_type')->default('all')->after('scheduled_at');
            $table->unsignedBigInteger('audience_id')->nullable()->after('audience_type');
            $table->boolean('is_priority')->default(false)->after('audience_id');
            $table->json('attachments')->nullable()->after('is_priority');
        });
    }

    public function down(): void
    {
        Schema::table('rf_announcements', function (Blueprint $table) {
            $table->dropColumn(['scheduled_at', 'audience_type', 'audience_id', 'is_priority', 'attachments']);
        });
    }
};
