<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            // Service request statuses (IDs 1-10)
            ['id' => 1, 'name' => 'New', 'name_ar' => 'جديد', 'name_en' => 'New', 'priority' => 1, 'type' => 'request'],
            ['id' => 2, 'name' => 'Assigned', 'name_ar' => 'تم التعيين', 'name_en' => 'Assigned', 'priority' => 2, 'type' => 'request'],
            ['id' => 3, 'name' => 'Resolved', 'name_ar' => 'تم الحل', 'name_en' => 'Resolved', 'priority' => 9, 'type' => 'request'],
            ['id' => 4, 'name' => 'Cancelled', 'name_ar' => 'تم الألغاء', 'name_en' => 'Cancelled', 'priority' => 10, 'type' => 'request'],
            ['id' => 5, 'name' => 'In Progress', 'name_ar' => 'جاري العمل', 'name_en' => 'In Progress', 'priority' => 5, 'type' => 'request'],
            ['id' => 6, 'name' => 'Request Accepted', 'name_ar' => 'تم قبول الطلب', 'name_en' => 'Request Accepted', 'priority' => 3, 'type' => 'request'],
            ['id' => 7, 'name' => 'Invoice Created', 'name_ar' => 'تم انشاء الفاتوره', 'name_en' => 'Invoice Created', 'priority' => 6, 'type' => 'request'],
            ['id' => 8, 'name' => 'Invoice Accepted', 'name_ar' => 'تم قبول الفاتوره', 'name_en' => 'Invoice Accepted', 'priority' => 7, 'type' => 'request'],
            ['id' => 9, 'name' => 'Invoice Rejected', 'name_ar' => 'تم رفض الفاتوره', 'name_en' => 'Invoice Rejected', 'priority' => 8, 'type' => 'request'],
            ['id' => 10, 'name' => 'Request Rejected', 'name_ar' => 'تم رفض الطلب', 'name_en' => 'Request Rejected', 'priority' => 4, 'type' => 'request'],

            // Visitor access statuses (IDs 11-17)
            ['id' => 11, 'name' => 'New', 'name_ar' => 'جديد', 'name_en' => 'New', 'priority' => 1, 'type' => 'visitor_access'],
            ['id' => 12, 'name' => 'Pending', 'name_ar' => 'في الانتظار', 'name_en' => 'Pending', 'priority' => 1, 'type' => 'visitor_access'],
            ['id' => 13, 'name' => 'Approved', 'name_ar' => 'موافق عليه', 'name_en' => 'Approved', 'priority' => 1, 'type' => 'visitor_access'],
            ['id' => 14, 'name' => 'Rejected', 'name_ar' => 'مرفوض', 'name_en' => 'Rejected', 'priority' => 1, 'type' => 'visitor_access'],
            ['id' => 15, 'name' => 'Cancelled', 'name_ar' => 'ألغي', 'name_en' => 'Cancelled', 'priority' => 1, 'type' => 'visitor_access'],
            ['id' => 16, 'name' => 'Checked In', 'name_ar' => 'تم تسجيل الدخول', 'name_en' => 'Checked In', 'priority' => 1, 'type' => 'visitor_access'],
            ['id' => 17, 'name' => 'Checked Out', 'name_ar' => 'تم تسجيل الخروج', 'name_en' => 'Checked Out', 'priority' => 1, 'type' => 'visitor_access'],

            // Facility booking statuses (IDs 19-22)
            ['id' => 19, 'name' => 'Pending Approval', 'name_ar' => 'في انتظار الموافقة', 'name_en' => 'Pending Approval', 'priority' => 1, 'type' => 'facility_booking'],
            ['id' => 20, 'name' => 'Booked', 'name_ar' => 'تم الحجز', 'name_en' => 'Booked', 'priority' => 1, 'type' => 'facility_booking'],
            ['id' => 21, 'name' => 'Booking Rejected', 'name_ar' => 'تم رفض الحجز', 'name_en' => 'Booking Rejected', 'priority' => 1, 'type' => 'facility_booking'],
            ['id' => 22, 'name' => 'Cancelled', 'name_ar' => 'تم الألغاء', 'name_en' => 'Cancelled', 'priority' => 1, 'type' => 'facility_booking'],

            // Unit statuses (IDs 23-26)
            ['id' => 23, 'name' => 'Sold', 'name_ar' => 'مباعة', 'name_en' => 'Sold', 'priority' => 1, 'type' => 'unit'],
            ['id' => 24, 'name' => 'Sold and Leased', 'name_ar' => 'مباعة و مؤجرة', 'name_en' => 'Sold and Leased', 'priority' => 1, 'type' => 'unit'],
            ['id' => 25, 'name' => 'Leased', 'name_ar' => 'مؤجرة', 'name_en' => 'Leased', 'priority' => 6, 'type' => 'unit'],
            ['id' => 26, 'name' => 'Vacant', 'name_ar' => 'متاحة', 'name_en' => 'Vacant', 'priority' => 1, 'type' => 'unit'],

            // Marketplace booking statuses (IDs 27-29)
            ['id' => 27, 'name' => 'New', 'name_ar' => 'جديد', 'name_en' => 'New', 'priority' => 1, 'type' => 'marketplace_booking'],
            ['id' => 28, 'name' => 'Booked', 'name_ar' => 'تم الحجز', 'name_en' => 'Booked', 'priority' => 1, 'type' => 'marketplace_booking'],
            ['id' => 29, 'name' => 'Cancelled', 'name_ar' => 'تم الألغاء', 'name_en' => 'Cancelled', 'priority' => 1, 'type' => 'marketplace_booking'],

            // Lease statuses (IDs 30-34)
            ['id' => 30, 'name' => 'New Contract', 'name_ar' => 'عقد جديد', 'name_en' => 'New Contract', 'priority' => 1, 'type' => 'lease'],
            ['id' => 31, 'name' => 'Active Contract', 'name_ar' => 'عقد ساري', 'name_en' => 'Active Contract', 'priority' => 1, 'type' => 'lease'],
            ['id' => 32, 'name' => 'Expired Contract', 'name_ar' => 'عقد منتهي', 'name_en' => 'Expired Contract', 'priority' => 1, 'type' => 'lease'],
            ['id' => 33, 'name' => 'Cancelled Contract', 'name_ar' => 'عقد ملغي', 'name_en' => 'Cancelled Contract', 'priority' => 1, 'type' => 'lease'],
            ['id' => 34, 'name' => 'Closed Contract', 'name_ar' => 'عقد مغلق', 'name_en' => 'Closed Contract', 'priority' => 1, 'type' => 'lease'],

            // Property visit statuses (IDs 35-38)
            ['id' => 35, 'name' => 'Scheduled', 'name_ar' => 'مجدول', 'name_en' => 'Scheduled', 'priority' => 1, 'type' => 'visit'],
            ['id' => 36, 'name' => 'Completed', 'name_ar' => 'مكتمل', 'name_en' => 'Completed', 'priority' => 1, 'type' => 'visit'],
            ['id' => 37, 'name' => 'Cancelled', 'name_ar' => 'ملغى', 'name_en' => 'Cancelled', 'priority' => 1, 'type' => 'visit'],
            ['id' => 38, 'name' => 'Rejected', 'name_ar' => 'مرفوض', 'name_en' => 'Rejected', 'priority' => 1, 'type' => 'visit'],

            // Off-plan sale reservation statuses (IDs 39-49)
            ['id' => 39, 'name' => 'Initial Booking Created', 'name_ar' => 'تم انشاء الحجز الأولي', 'name_en' => 'Initial Booking Created', 'priority' => 1, 'type' => 'sale_reservation'],
            ['id' => 40, 'name' => 'Approved', 'name_ar' => 'تمت الموافقة', 'name_en' => 'Approved', 'priority' => 1, 'type' => 'sale_reservation'],
            ['id' => 41, 'name' => 'Cancelled Before Deposit', 'name_ar' => 'الغاء قبل العربون', 'name_en' => 'Cancelled Before Deposit', 'priority' => 1, 'type' => 'sale_reservation'],
            ['id' => 42, 'name' => 'Booking Rejected', 'name_ar' => 'تم رفض الحجز', 'name_en' => 'Booking Rejected', 'priority' => 1, 'type' => 'sale_reservation'],
            ['id' => 43, 'name' => 'Cancelled After Deposit', 'name_ar' => 'الغاء بعد العربون', 'name_en' => 'Cancelled After Deposit', 'priority' => 1, 'type' => 'sale_reservation'],
            ['id' => 44, 'name' => 'Deposit Paid', 'name_ar' => 'تم دفع العربون', 'name_en' => 'Deposit Paid', 'priority' => 1, 'type' => 'sale_reservation'],
            ['id' => 45, 'name' => 'Contract Sent', 'name_ar' => 'تم إرسال العقد', 'name_en' => 'Contract Sent', 'priority' => 1, 'type' => 'sale_reservation'],
            ['id' => 46, 'name' => 'Payment Complete', 'name_ar' => 'اكتمل الدفع', 'name_en' => 'Payment Complete', 'priority' => 1, 'type' => 'sale_reservation'],
            ['id' => 47, 'name' => 'Contract Signed', 'name_ar' => 'تم توقيع العقد', 'name_en' => 'Contract Signed', 'priority' => 1, 'type' => 'sale_reservation'],
            ['id' => 48, 'name' => 'Ownership Transferred', 'name_ar' => 'تم نقل الملكية', 'name_en' => 'Ownership Transferred', 'priority' => 1, 'type' => 'sale_reservation'],
            ['id' => 49, 'name' => 'Contract Cancelled', 'name_ar' => 'تم الغاء العقد', 'name_en' => 'Contract Cancelled', 'priority' => 1, 'type' => 'sale_reservation'],

            // Reschedule status (ID 50)
            ['id' => 50, 'name' => 'Rescheduled', 'name_ar' => 'اعادة جدولة', 'name_en' => 'Rescheduled', 'priority' => 11, 'type' => 'request'],

            // Price quote statuses (IDs 52-58)
            ['id' => 52, 'name' => 'New', 'name_ar' => 'جديد', 'name_en' => 'New', 'priority' => 1, 'type' => 'price_quote'],
            ['id' => 53, 'name' => 'Approved', 'name_ar' => 'تمت الموافقة', 'name_en' => 'Approved', 'priority' => 2, 'type' => 'price_quote'],
            ['id' => 54, 'name' => 'Rejected', 'name_ar' => 'مرفوض', 'name_en' => 'Rejected', 'priority' => 3, 'type' => 'price_quote'],
            ['id' => 55, 'name' => 'Quote Sent', 'name_ar' => 'تم إرسال عرض السعر', 'name_en' => 'Quote Sent', 'priority' => 3, 'type' => 'price_quote'],
            ['id' => 56, 'name' => 'Rejected by Client', 'name_ar' => 'تم الرفض من العميل', 'name_en' => 'Rejected by Client', 'priority' => 3, 'type' => 'price_quote'],
            ['id' => 57, 'name' => 'Accepted by Client', 'name_ar' => 'تم القبول من العميل', 'name_en' => 'Accepted by Client', 'priority' => 3, 'type' => 'price_quote'],
            ['id' => 58, 'name' => 'Cancelled by Admin', 'name_ar' => 'تم الإلغاء من المسؤول', 'name_en' => 'Cancelled by Admin', 'priority' => 3, 'type' => 'price_quote'],

            // Invoice/transaction statuses (IDs 62-69)
            ['id' => 62, 'name' => 'Financial Data Review', 'name_ar' => 'مراجعة البيانات المالية', 'name_en' => 'Financial Data Review', 'priority' => 1, 'type' => 'invoice'],
            ['id' => 63, 'name' => 'Unit Payment Schedule', 'name_ar' => 'جدول دفع الوحدة', 'name_en' => 'Unit Payment Schedule', 'priority' => 1, 'type' => 'invoice'],
            ['id' => 64, 'name' => 'VAT Commission Payment', 'name_ar' => 'دفع عمولة ضريبة القيمة المضافة', 'name_en' => 'VAT Commission Payment', 'priority' => 1, 'type' => 'invoice'],
            ['id' => 65, 'name' => 'Pending Send', 'name_ar' => 'في انتظار الارسال', 'name_en' => 'Pending Send', 'priority' => 1, 'type' => 'invoice'],
            ['id' => 66, 'name' => 'Paid', 'name_ar' => 'تم الدفع', 'name_en' => 'Paid', 'priority' => 1, 'type' => 'invoice'],
            ['id' => 67, 'name' => 'Sent', 'name_ar' => 'تم الارسال', 'name_en' => 'Sent', 'priority' => 1, 'type' => 'invoice'],
            ['id' => 68, 'name' => 'Pending', 'name_ar' => 'في الانتظار', 'name_en' => 'Pending', 'priority' => 1, 'type' => 'invoice'],
            ['id' => 69, 'name' => 'Completed', 'name_ar' => 'تم اكتمال', 'name_en' => 'Completed', 'priority' => 1, 'type' => 'invoice'],

            // Lease quote statuses (IDs 70-75) — reserved for the lease-quote state machine
            // draft → sent → viewed → accepted | rejected | expired
            // IDs mirror App\Console\Commands\ExpireLeaseQuotes::STATUS_* constants.
            ['id' => 70, 'name' => 'draft', 'name_en' => 'draft', 'name_ar' => 'مسودة', 'priority' => 1, 'type' => 'lease_quote'],
            ['id' => 71, 'name' => 'sent', 'name_en' => 'sent', 'name_ar' => 'تم الإرسال', 'priority' => 2, 'type' => 'lease_quote'],
            ['id' => 72, 'name' => 'viewed', 'name_en' => 'viewed', 'name_ar' => 'تمت المشاهدة', 'priority' => 3, 'type' => 'lease_quote'],
            ['id' => 73, 'name' => 'accepted', 'name_en' => 'accepted', 'name_ar' => 'مقبول', 'priority' => 4, 'type' => 'lease_quote'],
            ['id' => 74, 'name' => 'rejected', 'name_en' => 'rejected', 'name_ar' => 'مرفوض', 'priority' => 5, 'type' => 'lease_quote'],
            ['id' => 75, 'name' => 'expired', 'name_en' => 'expired', 'name_ar' => 'منتهي الصلاحية', 'priority' => 6, 'type' => 'lease_quote'],
        ];

        DB::table('rf_statuses')->upsert(
            $statuses,
            ['id'],
            ['name', 'name_ar', 'name_en', 'priority', 'type'],
        );
    }
}
