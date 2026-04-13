<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flags = [
            // Contacts category
            ['key' => 'ENABLE_ADMIN', 'name' => 'Enable Admin Management', 'name_ar' => 'تفعيل إدارة المشرفين', 'category' => 'contacts', 'default_value' => true],
            ['key' => 'ENABLE_MANAGERS', 'name' => 'Enable Managers', 'name_ar' => 'تفعيل المديرين', 'category' => 'contacts', 'default_value' => true],
            ['key' => 'ENABLE_PROFESSIONALS', 'name' => 'Enable Professionals', 'name_ar' => 'تفعيل المهنيين', 'category' => 'contacts', 'default_value' => true],
            ['key' => 'ENABLE_TENANTS', 'name' => 'Enable Tenants', 'name_ar' => 'تفعيل المستأجرين', 'category' => 'contacts', 'default_value' => true],
            ['key' => 'ENABLE_DEPENDENTS', 'name' => 'Enable Dependents', 'name_ar' => 'تفعيل المعالين', 'category' => 'contacts', 'default_value' => true],
            ['key' => 'ENABLE_OWNERS', 'name' => 'Enable Owners', 'name_ar' => 'تفعيل الملاك', 'category' => 'contacts', 'default_value' => true],

            // Properties category
            ['key' => 'ENABLE_FACILITIES', 'name' => 'Enable Facilities', 'name_ar' => 'تفعيل المرافق', 'category' => 'properties', 'default_value' => true],
            ['key' => 'ENABLE_ADD_COMMON_AREA', 'name' => 'Enable Add Common Area', 'name_ar' => 'تفعيل إضافة المناطق المشتركة', 'category' => 'properties', 'default_value' => true],
            ['key' => 'ENABLE_COMMON_AREA_MANAGEMENT', 'name' => 'Enable Common Area Management', 'name_ar' => 'تفعيل إدارة المناطق المشتركة', 'category' => 'properties', 'default_value' => true],
            ['key' => 'ENABLE_PROPERTY_DOCUMENTATION', 'name' => 'Enable Property Documentation', 'name_ar' => 'تفعيل توثيق العقارات', 'category' => 'properties', 'default_value' => true],
            ['key' => 'ENABLE_PROPERTY_HANDOVER', 'name' => 'Enable Property Handover', 'name_ar' => 'تفعيل تسليم العقارات', 'category' => 'properties', 'default_value' => false],
            ['key' => 'ENABLE_ASSIGN_UNIT_OWNER', 'name' => 'Enable Assign Unit Owner', 'name_ar' => 'تفعيل تعيين مالك الوحدة', 'category' => 'properties', 'default_value' => true],
            ['key' => 'ENABLE_ASSIGN_UNIT_LEASE', 'name' => 'Enable Assign Unit Lease', 'name_ar' => 'تفعيل تعيين عقد الوحدة', 'category' => 'properties', 'default_value' => true],

            // Leasing category
            ['key' => 'CREATE_LEASES', 'name' => 'Create Leases', 'name_ar' => 'إنشاء العقود', 'category' => 'leasing', 'default_value' => true],
            ['key' => 'MOVE_IN_TENANTS', 'name' => 'Move In Tenants', 'name_ar' => 'تسكين المستأجرين', 'category' => 'leasing', 'default_value' => true],
            ['key' => 'MOVE_OUT_TENANTS', 'name' => 'Move Out Tenants', 'name_ar' => 'إخلاء المستأجرين', 'category' => 'leasing', 'default_value' => true],
            ['key' => 'INTEGRATE_WITH_EJAR', 'name' => 'Integrate with Ejar', 'name_ar' => 'التكامل مع إيجار', 'category' => 'leasing', 'default_value' => true],
            ['key' => 'ENABLE_AI_BASED_EJAR_CONTRACT_READER', 'name' => 'Enable AI Based Ejar Contract Reader', 'name_ar' => 'تفعيل قارئ العقود بالذكاء الاصطناعي', 'category' => 'leasing', 'default_value' => true],
            ['key' => 'ENABLE_UPLOAD_EJAR', 'name' => 'Enable Upload Ejar', 'name_ar' => 'تفعيل رفع عقود إيجار', 'category' => 'leasing', 'default_value' => true],

            // Transactions category
            ['key' => 'ENABLE_RECORD_TRANSACTION', 'name' => 'Enable Record Transaction', 'name_ar' => 'تفعيل تسجيل المعاملات', 'category' => 'transactions', 'default_value' => true],
            ['key' => 'ENABLE_RECORD_PAYMENT', 'name' => 'Enable Record Payment', 'name_ar' => 'تفعيل تسجيل المدفوعات', 'category' => 'transactions', 'default_value' => true],
            ['key' => 'ENABLE_PAYMENT_RECEIPTS', 'name' => 'Enable Payment Receipts', 'name_ar' => 'تفعيل إيصالات الدفع', 'category' => 'transactions', 'default_value' => true],
            ['key' => 'ENABLE_PAYMENT_REMINDER', 'name' => 'Enable Payment Reminder', 'name_ar' => 'تفعيل تذكير الدفع', 'category' => 'transactions', 'default_value' => true],
            ['key' => 'ENABLE_E_INVOICE', 'name' => 'Enable E-Invoice', 'name_ar' => 'تفعيل الفاتورة الإلكترونية', 'category' => 'transactions', 'default_value' => true],
            ['key' => 'ENABLE_ONLINE_PAYMENT', 'name' => 'Enable Online Payment', 'name_ar' => 'تفعيل الدفع الإلكتروني', 'category' => 'transactions', 'default_value' => true],

            // Requests category
            ['key' => 'ENABLE_REQUESTS', 'name' => 'Enable Requests', 'name_ar' => 'تفعيل الطلبات', 'category' => 'requests', 'default_value' => true],
            ['key' => 'ENABLE_BOOKING_REQUESTS', 'name' => 'Enable Booking Requests', 'name_ar' => 'تفعيل طلبات الحجز', 'category' => 'requests', 'default_value' => true],
            ['key' => 'ENABLE_SERVICES_SETTINGS', 'name' => 'Enable Services Settings', 'name_ar' => 'تفعيل إعدادات الخدمات', 'category' => 'requests', 'default_value' => true],
            ['key' => 'ENABLE_VISITOR_ACCESS_MANAGEMENT', 'name' => 'Enable Visitor Access Management', 'name_ar' => 'تفعيل إدارة وصول الزوار', 'category' => 'requests', 'default_value' => true],
            ['key' => 'ENABLE_SERVICE_PROVIDER', 'name' => 'Enable Service Provider', 'name_ar' => 'تفعيل مزود الخدمة', 'category' => 'requests', 'default_value' => true],
            ['key' => 'ENABLE_SUGGESTION', 'name' => 'Enable Suggestion', 'name_ar' => 'تفعيل الاقتراحات', 'category' => 'requests', 'default_value' => true],

            // Communication category
            ['key' => 'ENABLE_OFFERS', 'name' => 'Enable Offers', 'name_ar' => 'تفعيل العروض', 'category' => 'communication', 'default_value' => true],
            ['key' => 'ENABLE_SEND_ANNOUNCEMENT', 'name' => 'Enable Send Announcement', 'name_ar' => 'تفعيل إرسال الإعلانات', 'category' => 'communication', 'default_value' => true],
            ['key' => 'ENABLE_DIRECTORY', 'name' => 'Enable Directory', 'name_ar' => 'تفعيل الدليل', 'category' => 'communication', 'default_value' => true],
            ['key' => 'ENABLE_PUSH_NOTIFICATION', 'name' => 'Enable Push Notification', 'name_ar' => 'تفعيل الإشعارات', 'category' => 'communication', 'default_value' => true],
            ['key' => 'ENABLE_SEND_SMS', 'name' => 'Enable Send SMS', 'name_ar' => 'تفعيل إرسال الرسائل', 'category' => 'communication', 'default_value' => true],
            ['key' => 'ENABLE_WHATSAPP_BUSINESS', 'name' => 'Enable WhatsApp Business', 'name_ar' => 'تفعيل واتساب بزنس', 'category' => 'communication', 'default_value' => true],

            // Reports category
            ['key' => 'ENABLE_DASHBOARD', 'name' => 'Enable Dashboard', 'name_ar' => 'تفعيل لوحة التحكم', 'category' => 'reports', 'default_value' => true],
            ['key' => 'ENABLE_REQUIRE_ATTENTION', 'name' => 'Enable Require Attention', 'name_ar' => 'تفعيل يتطلب الانتباه', 'category' => 'reports', 'default_value' => true],
            ['key' => 'ENABLE_MEASURE_PERFORMANCE', 'name' => 'Enable Measure Performance', 'name_ar' => 'تفعيل قياس الأداء', 'category' => 'reports', 'default_value' => true],
            ['key' => 'ENABLE_LEASE_REPORT', 'name' => 'Enable Lease Report', 'name_ar' => 'تفعيل تقرير العقود', 'category' => 'reports', 'default_value' => true],
            ['key' => 'ENABLE_FINANCIAL_REPORT', 'name' => 'Enable Financial Report', 'name_ar' => 'تفعيل التقرير المالي', 'category' => 'reports', 'default_value' => true],
            ['key' => 'ENABLE_TENANT_REPORT', 'name' => 'Enable Tenant Report', 'name_ar' => 'تفعيل تقرير المستأجرين', 'category' => 'reports', 'default_value' => true],
            ['key' => 'ENABLE_MAINTENANCE_REPORT', 'name' => 'Enable Maintenance Report', 'name_ar' => 'تفعيل تقرير الصيانة', 'category' => 'reports', 'default_value' => true],

            // Tools category
            ['key' => 'ENABLE_TOOLS', 'name' => 'Enable Tools', 'name_ar' => 'تفعيل الأدوات', 'category' => 'tools', 'default_value' => true],
            ['key' => 'ENABLE_TOOLS_SETTINGS', 'name' => 'Enable Tools Settings', 'name_ar' => 'تفعيل إعدادات الأدوات', 'category' => 'tools', 'default_value' => true],
            ['key' => 'ENABLE_FORM_SETTINGS', 'name' => 'Enable Form Settings', 'name_ar' => 'تفعيل إعدادات النماذج', 'category' => 'tools', 'default_value' => true],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::updateOrCreate(
                ['key' => $flag['key']],
                array_merge($flag, ['is_active' => true])
            );
        }
    }
}
