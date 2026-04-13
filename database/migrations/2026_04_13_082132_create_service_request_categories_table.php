<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_request_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('name_ar', 100)->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('has_sub_categories')->default(false);
            $table->unsignedBigInteger('icon_id')->nullable();
            $table->json('service_settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('active');
        });

        // Seed default categories
        DB::table('service_request_categories')->insert([
            [
                'id' => 1,
                'name' => 'Unit Services',
                'name_ar' => 'خدمات الوحدات',
                'description' => 'Services for units',
                'description_ar' => 'للخدمات الخاصة بالوحدات',
                'active' => true,
                'has_sub_categories' => true,
                'service_settings' => json_encode([
                    'visibilities' => [
                        'hide_resident_number' => false,
                        'hide_resident_name' => false,
                        'hide_professional_number_and_name' => false,
                        'show_unified_number_only' => false,
                    ],
                    'permissions' => [
                        'manager_close_Request' => false,
                        'not_require_professional_enter_request_code' => false,
                        'not_require_professional_upload_request_photo' => false,
                        'attachments_required' => false,
                        'allow_professional_reschedule' => false,
                    ],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Common Area Requests',
                'name_ar' => 'طلبات المناطق المشتركة',
                'description' => 'Services for common areas',
                'description_ar' => 'للخدمات الخاصة بالمناطق المشتركة',
                'active' => true,
                'has_sub_categories' => true,
                'service_settings' => json_encode([
                    'visibilities' => [
                        'hide_resident_number' => false,
                        'hide_resident_name' => false,
                        'hide_professional_number_and_name' => false,
                        'show_unified_number_only' => false,
                    ],
                    'permissions' => [
                        'manager_close_Request' => false,
                        'not_require_professional_enter_request_code' => false,
                        'not_require_professional_upload_request_photo' => false,
                        'attachments_required' => false,
                        'allow_professional_reschedule' => false,
                    ],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Visitor Access Requests',
                'name_ar' => 'طلبات تصاريح الزوار',
                'description' => 'Visitor entry permit requests',
                'description_ar' => 'لطلبات تصاريح دخول الزوار',
                'active' => true,
                'has_sub_categories' => false,
                'service_settings' => json_encode([
                    'visibilities' => [
                        'hide_resident_number' => false,
                        'hide_resident_name' => false,
                        'hide_professional_number_and_name' => false,
                        'show_unified_number_only' => false,
                    ],
                    'permissions' => [
                        'manager_close_Request' => false,
                        'not_require_professional_enter_request_code' => false,
                        'not_require_professional_upload_request_photo' => false,
                        'attachments_required' => false,
                        'allow_professional_reschedule' => false,
                    ],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_categories');
    }
};
