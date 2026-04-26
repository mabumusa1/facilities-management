<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('rf_tenants', 'deleted_at')) {
            Schema::table('rf_tenants', function (Blueprint $table) {
                $table->softDeletes();
                $table->string('status')->default('active')->after('deleted_at');
            });
        } else {
            Schema::table('rf_tenants', function (Blueprint $table) {
                if (! Schema::hasColumn('rf_tenants', 'status')) {
                    $table->string('status')->default('active');
                }
            });
        }

        if (! Schema::hasColumn('rf_owners', 'deleted_at')) {
            Schema::table('rf_owners', function (Blueprint $table) {
                $table->softDeletes();
                $table->string('status')->default('active')->after('deleted_at');
            });
        } else {
            Schema::table('rf_owners', function (Blueprint $table) {
                if (! Schema::hasColumn('rf_owners', 'status')) {
                    $table->string('status')->default('active');
                }
            });
        }

        Schema::table('rf_professionals', function (Blueprint $table) {
            $table->softDeletes();
            $table->string('status')->default('active')->after('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('rf_tenants', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('status');
        });

        Schema::table('rf_owners', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('status');
        });

        Schema::table('rf_professionals', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('status');
        });
    }
};
