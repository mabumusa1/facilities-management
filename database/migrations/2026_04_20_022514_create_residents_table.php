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
        Schema::create('rf_tenants', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone_number');
            $table->string('national_phone_number')->nullable();
            $table->string('phone_country_code');
            $table->string('national_id')->nullable();
            $table->unsignedBigInteger('nationality_id')->nullable();
            $table->string('gender')->nullable();
            $table->date('georgian_birthdate')->nullable();
            $table->string('image')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('last_active')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->boolean('accepted_invite')->default(false);
            $table->string('relation')->nullable();
            $table->string('relation_key')->nullable();
            $table->unsignedBigInteger('account_tenant_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rf_tenants');
    }
};
