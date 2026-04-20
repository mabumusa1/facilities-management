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
        Schema::table('rf_announcements', function (Blueprint $table) {
            $table->renameColumn('body', 'content');
            $table->renameColumn('is_published', 'status');
        });

        Schema::table('rf_announcements', function (Blueprint $table) {
            $table->dropColumn(['priority', 'expires_at']);
        });

        Schema::table('rf_announcements', function (Blueprint $table) {
            $table->foreignId('building_id')->nullable()->after('community_id')->constrained('rf_buildings')->nullOnDelete();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->after('building_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('rf_announcements', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
            $table->dropColumn(['building_id', 'account_tenant_id']);
        });

        Schema::table('rf_announcements', function (Blueprint $table) {
            $table->string('priority')->default('normal');
            $table->timestamp('expires_at')->nullable();
        });

        Schema::table('rf_announcements', function (Blueprint $table) {
            $table->renameColumn('content', 'body');
            $table->renameColumn('status', 'is_published');
        });
    }
};
