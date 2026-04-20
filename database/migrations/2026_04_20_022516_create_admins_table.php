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
        Schema::create('rf_admins', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone_number');
            $table->string('full_phone_number')->nullable();
            $table->string('phone_country_code');
            $table->string('national_id')->nullable();
            $table->unsignedBigInteger('nationality_id')->nullable();
            $table->string('gender')->nullable();
            $table->date('georgian_birthdate')->nullable();
            $table->string('image')->nullable();
            $table->string('role');
            $table->boolean('active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_admins');
    }
};
