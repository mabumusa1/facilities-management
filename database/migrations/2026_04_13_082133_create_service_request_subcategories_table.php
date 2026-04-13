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
        Schema::create('service_request_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('service_request_categories')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('name_ar', 100)->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('icon_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'active']);
        });

        // Seed default subcategories for Unit Services (category_id = 1)
        DB::table('service_request_subcategories')->insert([
            ['id' => 1, 'category_id' => 1, 'name' => 'Maintenance', 'name_ar' => 'صيانة', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'category_id' => 1, 'name' => 'House Cleaning', 'name_ar' => 'تنظيف المنزل', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'category_id' => 1, 'name' => 'Car Wash', 'name_ar' => 'غسيل السيارات', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'category_id' => 1, 'name' => 'Electrical Appliances', 'name_ar' => 'الأجهزة الكهربائية', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'category_id' => 1, 'name' => 'Furniture Repair', 'name_ar' => 'إصلاح الأثاث', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'category_id' => 1, 'name' => 'Other Services', 'name_ar' => 'خدمات أخرى', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Seed default subcategories for Common Area Requests (category_id = 2)
        DB::table('service_request_subcategories')->insert([
            ['id' => 7, 'category_id' => 2, 'name' => 'Security & Safety', 'name_ar' => 'الأمن و السلامة', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'category_id' => 2, 'name' => 'Unit Issues', 'name_ar' => 'مشاكل الوحدات', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'category_id' => 2, 'name' => 'Resident Issues', 'name_ar' => 'مشاكل السكان', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'category_id' => 2, 'name' => 'Service Issues', 'name_ar' => 'مشاكل الخدمات', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'category_id' => 2, 'name' => 'Other Issues', 'name_ar' => 'مشاكل اخرى', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_subcategories');
    }
};
