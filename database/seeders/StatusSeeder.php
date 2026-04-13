<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            // Service Request statuses (1-10)
            ['id' => 1, 'domain' => 'service_request', 'slug' => 'service_request_new', 'name' => 'New', 'name_ar' => 'جديد', 'color' => '#3B82F6', 'icon' => 'plus-circle', 'priority' => 1],
            ['id' => 2, 'domain' => 'service_request', 'slug' => 'service_request_assigned', 'name' => 'Assigned', 'name_ar' => 'تم التعيين', 'color' => '#8B5CF6', 'icon' => 'user-check', 'priority' => 2],
            ['id' => 3, 'domain' => 'service_request', 'slug' => 'service_request_resolved', 'name' => 'Resolved', 'name_ar' => 'تم الحل', 'color' => '#22C55E', 'icon' => 'check-circle', 'priority' => 9],
            ['id' => 4, 'domain' => 'service_request', 'slug' => 'service_request_cancelled', 'name' => 'Cancelled', 'name_ar' => 'تم الألغاء', 'color' => '#6B7280', 'icon' => 'x-circle', 'priority' => 10],
            ['id' => 5, 'domain' => 'service_request', 'slug' => 'service_request_in_progress', 'name' => 'In Progress', 'name_ar' => 'جاري العمل', 'color' => '#F59E0B', 'icon' => 'clock', 'priority' => 5],
            ['id' => 6, 'domain' => 'service_request', 'slug' => 'service_request_accepted', 'name' => 'Request Accepted', 'name_ar' => 'تم قبول الطلب', 'color' => '#10B981', 'icon' => 'check', 'priority' => 3],
            ['id' => 7, 'domain' => 'service_request', 'slug' => 'service_request_invoice_created', 'name' => 'Invoice Created', 'name_ar' => 'تم انشاء الفاتوره', 'color' => '#6366F1', 'icon' => 'file-text', 'priority' => 6],
            ['id' => 8, 'domain' => 'service_request', 'slug' => 'service_request_invoice_accepted', 'name' => 'Invoice Accepted', 'name_ar' => 'تم قبول الفاتوره', 'color' => '#14B8A6', 'icon' => 'file-check', 'priority' => 7],
            ['id' => 9, 'domain' => 'service_request', 'slug' => 'service_request_invoice_rejected', 'name' => 'Invoice Rejected', 'name_ar' => 'تم رفض الفاتوره', 'color' => '#EF4444', 'icon' => 'file-x', 'priority' => 8],
            ['id' => 10, 'domain' => 'service_request', 'slug' => 'service_request_rejected', 'name' => 'Request Rejected', 'name_ar' => 'تم رفض الطلب', 'color' => '#DC2626', 'icon' => 'x', 'priority' => 4],

            // Visitor Access statuses (11-17)
            ['id' => 11, 'domain' => 'visitor_access', 'slug' => 'visitor_access_new', 'name' => 'New', 'name_ar' => 'جديد', 'color' => '#3B82F6', 'icon' => 'plus-circle', 'priority' => 1],
            ['id' => 12, 'domain' => 'visitor_access', 'slug' => 'visitor_access_pending', 'name' => 'Pending', 'name_ar' => 'في الانتظار', 'color' => '#F59E0B', 'icon' => 'clock', 'priority' => 2],
            ['id' => 13, 'domain' => 'visitor_access', 'slug' => 'visitor_access_approved', 'name' => 'Approved', 'name_ar' => 'موافق عليه', 'color' => '#22C55E', 'icon' => 'check-circle', 'priority' => 3],
            ['id' => 14, 'domain' => 'visitor_access', 'slug' => 'visitor_access_rejected', 'name' => 'Rejected', 'name_ar' => 'مرفوض', 'color' => '#EF4444', 'icon' => 'x-circle', 'priority' => 4],
            ['id' => 15, 'domain' => 'visitor_access', 'slug' => 'visitor_access_cancelled', 'name' => 'Cancelled', 'name_ar' => 'ألغي', 'color' => '#6B7280', 'icon' => 'x', 'priority' => 5],
            ['id' => 16, 'domain' => 'visitor_access', 'slug' => 'visitor_access_checked_in', 'name' => 'Checked In', 'name_ar' => 'تم تسجيل الدخول', 'color' => '#10B981', 'icon' => 'log-in', 'priority' => 6],
            ['id' => 17, 'domain' => 'visitor_access', 'slug' => 'visitor_access_checked_out', 'name' => 'Checked Out', 'name_ar' => 'تم تسجيل الخروج', 'color' => '#6366F1', 'icon' => 'log-out', 'priority' => 7],

            // Facility Booking statuses (19-22)
            ['id' => 19, 'domain' => 'facility_booking', 'slug' => 'facility_booking_pending', 'name' => 'Pending Approval', 'name_ar' => 'في انتظار الموافقة', 'color' => '#F59E0B', 'icon' => 'clock', 'priority' => 1],
            ['id' => 20, 'domain' => 'facility_booking', 'slug' => 'facility_booking_booked', 'name' => 'Booked', 'name_ar' => 'تم الحجز', 'color' => '#22C55E', 'icon' => 'calendar-check', 'priority' => 2],
            ['id' => 21, 'domain' => 'facility_booking', 'slug' => 'facility_booking_rejected', 'name' => 'Booking Rejected', 'name_ar' => 'تم رفض الحجز', 'color' => '#EF4444', 'icon' => 'calendar-x', 'priority' => 3],
            ['id' => 22, 'domain' => 'facility_booking', 'slug' => 'facility_booking_cancelled', 'name' => 'Cancelled', 'name_ar' => 'تم الألغاء', 'color' => '#6B7280', 'icon' => 'x-circle', 'priority' => 4],

            // Marketplace Unit statuses (23-26)
            ['id' => 23, 'domain' => 'marketplace_unit', 'slug' => 'marketplace_unit_sold', 'name' => 'Sold', 'name_ar' => 'مباعة', 'color' => '#22C55E', 'icon' => 'badge-check', 'priority' => 1],
            ['id' => 24, 'domain' => 'marketplace_unit', 'slug' => 'marketplace_unit_sold_rented', 'name' => 'Sold & Rented', 'name_ar' => 'مباعة و مؤجرة', 'color' => '#8B5CF6', 'icon' => 'home', 'priority' => 2],
            ['id' => 25, 'domain' => 'marketplace_unit', 'slug' => 'marketplace_unit_rented', 'name' => 'Rented', 'name_ar' => 'مؤجرة', 'color' => '#6366F1', 'icon' => 'key', 'priority' => 6],
            ['id' => 26, 'domain' => 'marketplace_unit', 'slug' => 'marketplace_unit_available', 'name' => 'Available', 'name_ar' => 'متاحة', 'color' => '#3B82F6', 'icon' => 'tag', 'priority' => 3],

            // Marketplace Visit statuses (27-29)
            ['id' => 27, 'domain' => 'marketplace_visit', 'slug' => 'marketplace_visit_new', 'name' => 'New', 'name_ar' => 'جديد', 'color' => '#3B82F6', 'icon' => 'plus-circle', 'priority' => 1],
            ['id' => 28, 'domain' => 'marketplace_visit', 'slug' => 'marketplace_visit_booked', 'name' => 'Booked', 'name_ar' => 'تم الحجز', 'color' => '#22C55E', 'icon' => 'calendar-check', 'priority' => 2],
            ['id' => 29, 'domain' => 'marketplace_visit', 'slug' => 'marketplace_visit_cancelled', 'name' => 'Cancelled', 'name_ar' => 'تم الألغاء', 'color' => '#6B7280', 'icon' => 'x-circle', 'priority' => 3],

            // Lease statuses (30-34)
            ['id' => 30, 'domain' => 'lease', 'slug' => 'lease_draft', 'name' => 'Draft', 'name_ar' => 'عقد جديد', 'color' => '#6B7280', 'icon' => 'file', 'priority' => 1],
            ['id' => 31, 'domain' => 'lease', 'slug' => 'lease_active', 'name' => 'Active', 'name_ar' => 'عقد ساري', 'color' => '#22C55E', 'icon' => 'check-circle', 'priority' => 2],
            ['id' => 32, 'domain' => 'lease', 'slug' => 'lease_expired', 'name' => 'Expired', 'name_ar' => 'عقد منتهي', 'color' => '#F59E0B', 'icon' => 'alert-triangle', 'priority' => 3],
            ['id' => 33, 'domain' => 'lease', 'slug' => 'lease_terminated', 'name' => 'Terminated', 'name_ar' => 'عقد ملغي', 'color' => '#EF4444', 'icon' => 'x-circle', 'priority' => 4],
            ['id' => 34, 'domain' => 'lease', 'slug' => 'lease_closed', 'name' => 'Closed', 'name_ar' => 'عقد مغلق', 'color' => '#1F2937', 'icon' => 'archive', 'priority' => 5],

            // Visit Scheduling statuses (35-38, 50)
            ['id' => 35, 'domain' => 'visit_scheduling', 'slug' => 'visit_scheduling_scheduled', 'name' => 'Scheduled', 'name_ar' => 'مجدول', 'color' => '#3B82F6', 'icon' => 'calendar', 'priority' => 1],
            ['id' => 36, 'domain' => 'visit_scheduling', 'slug' => 'visit_scheduling_completed', 'name' => 'Completed', 'name_ar' => 'مكتمل', 'color' => '#22C55E', 'icon' => 'check-circle', 'priority' => 2],
            ['id' => 37, 'domain' => 'visit_scheduling', 'slug' => 'visit_scheduling_cancelled', 'name' => 'Cancelled', 'name_ar' => 'ملغى', 'color' => '#6B7280', 'icon' => 'x-circle', 'priority' => 3],
            ['id' => 38, 'domain' => 'visit_scheduling', 'slug' => 'visit_scheduling_rejected', 'name' => 'Rejected', 'name_ar' => 'مرفوض', 'color' => '#EF4444', 'icon' => 'x', 'priority' => 4],
            ['id' => 50, 'domain' => 'visit_scheduling', 'slug' => 'visit_scheduling_rescheduled', 'name' => 'Rescheduled', 'name_ar' => 'اعادة جدولة', 'color' => '#F59E0B', 'icon' => 'refresh-cw', 'priority' => 11],

            // Booking Contract statuses (39-49)
            ['id' => 39, 'domain' => 'booking_contract', 'slug' => 'booking_contract_initial_created', 'name' => 'Initial Booking Created', 'name_ar' => 'تم انشاء الحجز الأولي', 'color' => '#3B82F6', 'icon' => 'file-plus', 'priority' => 1],
            ['id' => 40, 'domain' => 'booking_contract', 'slug' => 'booking_contract_approved', 'name' => 'Approved', 'name_ar' => 'تمت الموافقة', 'color' => '#22C55E', 'icon' => 'check', 'priority' => 2],
            ['id' => 41, 'domain' => 'booking_contract', 'slug' => 'booking_contract_cancelled_before_deposit', 'name' => 'Cancelled Before Deposit', 'name_ar' => 'الغاء قبل العربون', 'color' => '#6B7280', 'icon' => 'x', 'priority' => 3],
            ['id' => 42, 'domain' => 'booking_contract', 'slug' => 'booking_contract_rejected', 'name' => 'Booking Rejected', 'name_ar' => 'تم رفض الحجز', 'color' => '#EF4444', 'icon' => 'x-circle', 'priority' => 4],
            ['id' => 43, 'domain' => 'booking_contract', 'slug' => 'booking_contract_cancelled_after_deposit', 'name' => 'Cancelled After Deposit', 'name_ar' => 'الغاء بعد العربون', 'color' => '#DC2626', 'icon' => 'x', 'priority' => 5],
            ['id' => 44, 'domain' => 'booking_contract', 'slug' => 'booking_contract_deposit_paid', 'name' => 'Deposit Paid', 'name_ar' => 'تم دفع العربون', 'color' => '#10B981', 'icon' => 'credit-card', 'priority' => 6],
            ['id' => 45, 'domain' => 'booking_contract', 'slug' => 'booking_contract_contract_sent', 'name' => 'Contract Sent', 'name_ar' => 'تم إرسال العقد', 'color' => '#6366F1', 'icon' => 'send', 'priority' => 7],
            ['id' => 46, 'domain' => 'booking_contract', 'slug' => 'booking_contract_payment_complete', 'name' => 'Payment Complete', 'name_ar' => 'اكتمل الدفع', 'color' => '#22C55E', 'icon' => 'check-square', 'priority' => 8],
            ['id' => 47, 'domain' => 'booking_contract', 'slug' => 'booking_contract_signed', 'name' => 'Contract Signed', 'name_ar' => 'تم توقيع العقد', 'color' => '#14B8A6', 'icon' => 'pen-tool', 'priority' => 9],
            ['id' => 48, 'domain' => 'booking_contract', 'slug' => 'booking_contract_ownership_transferred', 'name' => 'Ownership Transferred', 'name_ar' => 'تم نقل الملكية', 'color' => '#8B5CF6', 'icon' => 'home', 'priority' => 10],
            ['id' => 49, 'domain' => 'booking_contract', 'slug' => 'booking_contract_cancelled', 'name' => 'Contract Cancelled', 'name_ar' => 'تم الغاء العقد', 'color' => '#1F2937', 'icon' => 'file-x', 'priority' => 11],

            // Application/Quote statuses (52-58)
            ['id' => 52, 'domain' => 'application', 'slug' => 'application_new', 'name' => 'New', 'name_ar' => 'جديد', 'color' => '#3B82F6', 'icon' => 'plus-circle', 'priority' => 1],
            ['id' => 53, 'domain' => 'application', 'slug' => 'application_approved', 'name' => 'Approved', 'name_ar' => 'تمت الموافقة', 'color' => '#22C55E', 'icon' => 'check-circle', 'priority' => 2],
            ['id' => 54, 'domain' => 'application', 'slug' => 'application_rejected', 'name' => 'Rejected', 'name_ar' => 'مرفوض', 'color' => '#EF4444', 'icon' => 'x-circle', 'priority' => 3],
            ['id' => 55, 'domain' => 'application', 'slug' => 'application_quote_sent', 'name' => 'Quote Sent', 'name_ar' => 'تم إرسال عرض السعر', 'color' => '#6366F1', 'icon' => 'mail', 'priority' => 4],
            ['id' => 56, 'domain' => 'application', 'slug' => 'application_customer_rejected', 'name' => 'Rejected by Customer', 'name_ar' => 'تم الرفض من العميل', 'color' => '#DC2626', 'icon' => 'user-x', 'priority' => 5],
            ['id' => 57, 'domain' => 'application', 'slug' => 'application_customer_accepted', 'name' => 'Accepted by Customer', 'name_ar' => 'تم القبول من العميل', 'color' => '#10B981', 'icon' => 'user-check', 'priority' => 6],
            ['id' => 58, 'domain' => 'application', 'slug' => 'application_admin_cancelled', 'name' => 'Cancelled by Admin', 'name_ar' => 'تم الإلغاء من المسؤول', 'color' => '#6B7280', 'icon' => 'shield-off', 'priority' => 7],

            // Transaction statuses (62-69)
            ['id' => 62, 'domain' => 'transaction', 'slug' => 'transaction_financial_review', 'name' => 'Financial Review', 'name_ar' => 'مراجعة البيانات المالية', 'color' => '#8B5CF6', 'icon' => 'search', 'priority' => 1],
            ['id' => 63, 'domain' => 'transaction', 'slug' => 'transaction_payment_schedule', 'name' => 'Unit Payment Schedule', 'name_ar' => 'جدول دفع الوحدة', 'color' => '#6366F1', 'icon' => 'calendar', 'priority' => 2],
            ['id' => 64, 'domain' => 'transaction', 'slug' => 'transaction_vat_commission', 'name' => 'VAT Commission Payment', 'name_ar' => 'دفع عمولة ضريبة القيمة المضافة', 'color' => '#F59E0B', 'icon' => 'percent', 'priority' => 3],
            ['id' => 65, 'domain' => 'transaction', 'slug' => 'transaction_pending_sending', 'name' => 'Pending Sending', 'name_ar' => 'في انتظار الارسال', 'color' => '#F59E0B', 'icon' => 'clock', 'priority' => 4],
            ['id' => 66, 'domain' => 'transaction', 'slug' => 'transaction_paid', 'name' => 'Paid', 'name_ar' => 'تم الدفع', 'color' => '#22C55E', 'icon' => 'check-circle', 'priority' => 5],
            ['id' => 67, 'domain' => 'transaction', 'slug' => 'transaction_sent', 'name' => 'Sent', 'name_ar' => 'تم الارسال', 'color' => '#3B82F6', 'icon' => 'send', 'priority' => 6],
            ['id' => 68, 'domain' => 'transaction', 'slug' => 'transaction_pending', 'name' => 'Pending', 'name_ar' => 'في الانتظار', 'color' => '#F59E0B', 'icon' => 'clock', 'priority' => 7],
            ['id' => 69, 'domain' => 'transaction', 'slug' => 'transaction_completed', 'name' => 'Completed', 'name_ar' => 'تم اكتمال', 'color' => '#22C55E', 'icon' => 'check-square', 'priority' => 8],

            // Unit statuses (additional)
            ['id' => 70, 'domain' => 'unit', 'slug' => 'unit_vacant', 'name' => 'Vacant', 'name_ar' => 'شاغرة', 'color' => '#22C55E', 'icon' => 'home', 'priority' => 1],
            ['id' => 71, 'domain' => 'unit', 'slug' => 'unit_occupied', 'name' => 'Occupied', 'name_ar' => 'مشغولة', 'color' => '#3B82F6', 'icon' => 'users', 'priority' => 2],
            ['id' => 72, 'domain' => 'unit', 'slug' => 'unit_under_maintenance', 'name' => 'Under Maintenance', 'name_ar' => 'تحت الصيانة', 'color' => '#F59E0B', 'icon' => 'tool', 'priority' => 3],
            ['id' => 73, 'domain' => 'unit', 'slug' => 'unit_reserved', 'name' => 'Reserved', 'name_ar' => 'محجوزة', 'color' => '#8B5CF6', 'icon' => 'bookmark', 'priority' => 4],
        ];

        foreach ($statuses as $status) {
            Status::updateOrCreate(
                ['id' => $status['id']],
                $status
            );
        }
    }
}
