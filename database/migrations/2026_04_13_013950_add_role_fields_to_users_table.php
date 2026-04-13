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
        Schema::table('users', function (Blueprint $table) {
            $table->string('contact_type')->nullable()->after('email');
            $table->unsignedTinyInteger('manager_role')->nullable()->after('contact_type');
            $table->unsignedTinyInteger('service_manager_type')->nullable()->after('manager_role');
            $table->index('contact_type');
            $table->index('manager_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['contact_type']);
            $table->dropIndex(['manager_role']);
            $table->dropColumn(['contact_type', 'manager_role', 'service_manager_type']);
        });
    }
};
