<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            // Request statuses (1-11, 50)
            ['id' => 1, 'name' => 'New', 'name_ar' => 'جديد', 'name_en' => 'New', 'priority' => 1, 'type' => 'request'],
            ['id' => 2, 'name' => 'Open', 'name_ar' => 'مفتوح', 'name_en' => 'Open', 'priority' => 2, 'type' => 'request'],
            ['id' => 3, 'name' => 'In Progress', 'name_ar' => 'قيد التنفيذ', 'name_en' => 'In Progress', 'priority' => 3, 'type' => 'request'],
            ['id' => 4, 'name' => 'Pending', 'name_ar' => 'معلق', 'name_en' => 'Pending', 'priority' => 4, 'type' => 'request'],
            ['id' => 5, 'name' => 'Assigned', 'name_ar' => 'مُعيّن', 'name_en' => 'Assigned', 'priority' => 5, 'type' => 'request'],
            ['id' => 6, 'name' => 'On the Way', 'name_ar' => 'في الطريق', 'name_en' => 'On the Way', 'priority' => 6, 'type' => 'request'],
            ['id' => 7, 'name' => 'Started', 'name_ar' => 'بدأ', 'name_en' => 'Started', 'priority' => 7, 'type' => 'request'],
            ['id' => 8, 'name' => 'Completed', 'name_ar' => 'مكتمل', 'name_en' => 'Completed', 'priority' => 8, 'type' => 'request'],
            ['id' => 9, 'name' => 'Closed', 'name_ar' => 'مغلق', 'name_en' => 'Closed', 'priority' => 9, 'type' => 'request'],
            ['id' => 10, 'name' => 'Cancelled', 'name_ar' => 'ملغي', 'name_en' => 'Cancelled', 'priority' => 10, 'type' => 'request'],
            ['id' => 11, 'name' => 'Rejected', 'name_ar' => 'مرفوض', 'name_en' => 'Rejected', 'priority' => 11, 'type' => 'request'],
            ['id' => 50, 'name' => 'Rescheduled', 'name_ar' => 'معاد جدولته', 'name_en' => 'Rescheduled', 'priority' => 12, 'type' => 'request'],

            // Visitor access statuses (11-17) — 11 shared with request
            ['id' => 12, 'name' => 'Visitor Pending', 'name_ar' => 'بانتظار الموافقة', 'name_en' => 'Visitor Pending', 'priority' => 1, 'type' => 'visitor_access'],
            ['id' => 13, 'name' => 'Visitor Approved', 'name_ar' => 'موافق عليه', 'name_en' => 'Visitor Approved', 'priority' => 2, 'type' => 'visitor_access'],
            ['id' => 14, 'name' => 'Visitor Checked In', 'name_ar' => 'تم الدخول', 'name_en' => 'Visitor Checked In', 'priority' => 3, 'type' => 'visitor_access'],
            ['id' => 15, 'name' => 'Visitor Checked Out', 'name_ar' => 'تم الخروج', 'name_en' => 'Visitor Checked Out', 'priority' => 4, 'type' => 'visitor_access'],
            ['id' => 16, 'name' => 'Visitor Cancelled', 'name_ar' => 'ملغي', 'name_en' => 'Visitor Cancelled', 'priority' => 5, 'type' => 'visitor_access'],
            ['id' => 17, 'name' => 'Visitor Expired', 'name_ar' => 'منتهي الصلاحية', 'name_en' => 'Visitor Expired', 'priority' => 6, 'type' => 'visitor_access'],

            // Facility booking statuses (19-22)
            ['id' => 19, 'name' => 'Booking Pending', 'name_ar' => 'حجز معلق', 'name_en' => 'Booking Pending', 'priority' => 1, 'type' => 'facility_booking'],
            ['id' => 20, 'name' => 'Booking Confirmed', 'name_ar' => 'حجز مؤكد', 'name_en' => 'Booking Confirmed', 'priority' => 2, 'type' => 'facility_booking'],
            ['id' => 21, 'name' => 'Booking Cancelled', 'name_ar' => 'حجز ملغي', 'name_en' => 'Booking Cancelled', 'priority' => 3, 'type' => 'facility_booking'],
            ['id' => 22, 'name' => 'Booking Completed', 'name_ar' => 'حجز مكتمل', 'name_en' => 'Booking Completed', 'priority' => 4, 'type' => 'facility_booking'],

            // Unit statuses (23-26)
            ['id' => 23, 'name' => 'Vacant', 'name_ar' => 'شاغرة', 'name_en' => 'Vacant', 'priority' => 1, 'type' => 'unit'],
            ['id' => 24, 'name' => 'Occupied', 'name_ar' => 'مشغولة', 'name_en' => 'Occupied', 'priority' => 2, 'type' => 'unit'],
            ['id' => 25, 'name' => 'Under Maintenance', 'name_ar' => 'تحت الصيانة', 'name_en' => 'Under Maintenance', 'priority' => 3, 'type' => 'unit'],
            ['id' => 26, 'name' => 'Reserved', 'name_ar' => 'محجوزة', 'name_en' => 'Reserved', 'priority' => 4, 'type' => 'unit'],

            // Booking statuses (27-29)
            ['id' => 27, 'name' => 'Application Pending', 'name_ar' => 'طلب معلق', 'name_en' => 'Application Pending', 'priority' => 1, 'type' => 'booking'],
            ['id' => 28, 'name' => 'Application Approved', 'name_ar' => 'طلب موافق', 'name_en' => 'Application Approved', 'priority' => 2, 'type' => 'booking'],
            ['id' => 29, 'name' => 'Application Rejected', 'name_ar' => 'طلب مرفوض', 'name_en' => 'Application Rejected', 'priority' => 3, 'type' => 'booking'],

            // Lease statuses (30-34)
            ['id' => 30, 'name' => 'Draft', 'name_ar' => 'مسودة', 'name_en' => 'Draft', 'priority' => 1, 'type' => 'lease'],
            ['id' => 31, 'name' => 'Active', 'name_ar' => 'نشط', 'name_en' => 'Active', 'priority' => 2, 'type' => 'lease'],
            ['id' => 32, 'name' => 'Expired', 'name_ar' => 'منتهي', 'name_en' => 'Expired', 'priority' => 3, 'type' => 'lease'],
            ['id' => 33, 'name' => 'Terminated', 'name_ar' => 'منهي', 'name_en' => 'Terminated', 'priority' => 4, 'type' => 'lease'],
            ['id' => 34, 'name' => 'Renewed', 'name_ar' => 'مجدد', 'name_en' => 'Renewed', 'priority' => 5, 'type' => 'lease'],

            // Property visit statuses (35-38)
            ['id' => 35, 'name' => 'Visit Scheduled', 'name_ar' => 'زيارة مجدولة', 'name_en' => 'Visit Scheduled', 'priority' => 1, 'type' => 'property_visit'],
            ['id' => 36, 'name' => 'Visit Completed', 'name_ar' => 'زيارة مكتملة', 'name_en' => 'Visit Completed', 'priority' => 2, 'type' => 'property_visit'],
            ['id' => 37, 'name' => 'Visit Cancelled', 'name_ar' => 'زيارة ملغاة', 'name_en' => 'Visit Cancelled', 'priority' => 3, 'type' => 'property_visit'],
            ['id' => 38, 'name' => 'Visit No Show', 'name_ar' => 'لم يحضر', 'name_en' => 'Visit No Show', 'priority' => 4, 'type' => 'property_visit'],

            // Marketplace booking statuses (39-49)
            ['id' => 39, 'name' => 'Marketplace Pending', 'name_ar' => 'معلق', 'name_en' => 'Marketplace Pending', 'priority' => 1, 'type' => 'marketplace_booking'],
            ['id' => 40, 'name' => 'Marketplace Approved', 'name_ar' => 'موافق', 'name_en' => 'Marketplace Approved', 'priority' => 2, 'type' => 'marketplace_booking'],
            ['id' => 41, 'name' => 'Marketplace Rejected', 'name_ar' => 'مرفوض', 'name_en' => 'Marketplace Rejected', 'priority' => 3, 'type' => 'marketplace_booking'],
            ['id' => 42, 'name' => 'Marketplace Visit Scheduled', 'name_ar' => 'زيارة مجدولة', 'name_en' => 'Visit Scheduled', 'priority' => 4, 'type' => 'marketplace_booking'],
            ['id' => 43, 'name' => 'Marketplace Visit Done', 'name_ar' => 'تمت الزيارة', 'name_en' => 'Visit Done', 'priority' => 5, 'type' => 'marketplace_booking'],
            ['id' => 44, 'name' => 'Marketplace Negotiation', 'name_ar' => 'تفاوض', 'name_en' => 'Negotiation', 'priority' => 6, 'type' => 'marketplace_booking'],
            ['id' => 45, 'name' => 'Marketplace Contract Sent', 'name_ar' => 'تم إرسال العقد', 'name_en' => 'Contract Sent', 'priority' => 7, 'type' => 'marketplace_booking'],
            ['id' => 46, 'name' => 'Marketplace Contract Signed', 'name_ar' => 'تم توقيع العقد', 'name_en' => 'Contract Signed', 'priority' => 8, 'type' => 'marketplace_booking'],
            ['id' => 47, 'name' => 'Marketplace Closed Won', 'name_ar' => 'مغلق - ربح', 'name_en' => 'Closed Won', 'priority' => 9, 'type' => 'marketplace_booking'],
            ['id' => 48, 'name' => 'Marketplace Closed Lost', 'name_ar' => 'مغلق - خسارة', 'name_en' => 'Closed Lost', 'priority' => 10, 'type' => 'marketplace_booking'],
            ['id' => 49, 'name' => 'Marketplace On Hold', 'name_ar' => 'معلق', 'name_en' => 'On Hold', 'priority' => 11, 'type' => 'marketplace_booking'],

            // Offer request statuses (52-58)
            ['id' => 52, 'name' => 'Offer New', 'name_ar' => 'عرض جديد', 'name_en' => 'Offer New', 'priority' => 1, 'type' => 'offer_request'],
            ['id' => 53, 'name' => 'Offer Sent', 'name_ar' => 'عرض مرسل', 'name_en' => 'Offer Sent', 'priority' => 2, 'type' => 'offer_request'],
            ['id' => 54, 'name' => 'Offer Viewed', 'name_ar' => 'عرض معروض', 'name_en' => 'Offer Viewed', 'priority' => 3, 'type' => 'offer_request'],
            ['id' => 55, 'name' => 'Offer Accepted', 'name_ar' => 'عرض مقبول', 'name_en' => 'Offer Accepted', 'priority' => 4, 'type' => 'offer_request'],
            ['id' => 56, 'name' => 'Offer Rejected', 'name_ar' => 'عرض مرفوض', 'name_en' => 'Offer Rejected', 'priority' => 5, 'type' => 'offer_request'],
            ['id' => 57, 'name' => 'Offer Expired', 'name_ar' => 'عرض منتهي', 'name_en' => 'Offer Expired', 'priority' => 6, 'type' => 'offer_request'],
            ['id' => 58, 'name' => 'Offer Cancelled', 'name_ar' => 'عرض ملغي', 'name_en' => 'Offer Cancelled', 'priority' => 7, 'type' => 'offer_request'],

            // Property handover statuses (62-69)
            ['id' => 62, 'name' => 'Handover Scheduled', 'name_ar' => 'تسليم مجدول', 'name_en' => 'Handover Scheduled', 'priority' => 1, 'type' => 'property_handover'],
            ['id' => 63, 'name' => 'Inspection In Progress', 'name_ar' => 'فحص قيد التنفيذ', 'name_en' => 'Inspection In Progress', 'priority' => 2, 'type' => 'property_handover'],
            ['id' => 64, 'name' => 'Inspection Completed', 'name_ar' => 'فحص مكتمل', 'name_en' => 'Inspection Completed', 'priority' => 3, 'type' => 'property_handover'],
            ['id' => 65, 'name' => 'Pending Repairs', 'name_ar' => 'بانتظار الإصلاح', 'name_en' => 'Pending Repairs', 'priority' => 4, 'type' => 'property_handover'],
            ['id' => 66, 'name' => 'Repairs Completed', 'name_ar' => 'إصلاح مكتمل', 'name_en' => 'Repairs Completed', 'priority' => 5, 'type' => 'property_handover'],
            ['id' => 67, 'name' => 'Handover Completed', 'name_ar' => 'تسليم مكتمل', 'name_en' => 'Handover Completed', 'priority' => 6, 'type' => 'property_handover'],
            ['id' => 68, 'name' => 'Handover Cancelled', 'name_ar' => 'تسليم ملغي', 'name_en' => 'Handover Cancelled', 'priority' => 7, 'type' => 'property_handover'],
            ['id' => 69, 'name' => 'Handover Disputed', 'name_ar' => 'تسليم متنازع', 'name_en' => 'Handover Disputed', 'priority' => 8, 'type' => 'property_handover'],
        ];

        foreach ($statuses as $status) {
            Status::updateOrCreate(['id' => $status['id']], $status);
        }
    }
}
