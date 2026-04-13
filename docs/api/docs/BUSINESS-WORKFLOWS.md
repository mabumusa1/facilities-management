# Atar Business Workflows Reference

> Auto-generated from API captures and status analysis

Generated: 2026-04-12

---

## Overview

This document maps the state machines and business workflows for core entities in the Atar property management system.

---

## Status ID Reference

All statuses extracted from `GET /rf/statuses`:

### Unit Statuses (IDs 23-26)
| ID | Arabic | English | Description |
|----|--------|---------|-------------|
| 23 | مباعة | Sold | Unit has been sold |
| 24 | مباعة و مؤجرة | Sold & Rented | Unit sold but also rented |
| 25 | مؤجرة | Rented | Unit is currently rented |
| 26 | متاحة | Available | Unit is available |

### Lease Contract Statuses (IDs 30-34)
| ID | Arabic | English | Description |
|----|--------|---------|-------------|
| 30 | عقد جديد | New Contract | Draft/new lease contract |
| 31 | عقد ساري | Active Contract | Currently active lease |
| 32 | عقد منتهي | Expired Contract | Lease has expired |
| 33 | عقد ملغي | Canceled Contract | Lease was canceled |
| 34 | عقد مغلق | Closed Contract | Lease fully closed |

### Service Request Statuses (IDs 1-10, 52-58)
| ID | Arabic | English | Priority |
|----|--------|---------|----------|
| 1/52 | جديد | New | 1 |
| 2 | تم التعيين | Assigned | 2 |
| 53 | تمت الموافقة | Approved | 2 |
| 6 | تم قبول الطلب | Request Accepted | 3 |
| 54 | مرفوض | Rejected | 3 |
| 55 | تم إرسال عرض السعر | Quote Sent | 3 |
| 56 | تم الرفض من العميل | Rejected by Client | 3 |
| 57 | تم القبول من العميل | Accepted by Client | 3 |
| 58 | تم الإلغاء من المسؤول | Canceled by Admin | 3 |
| 10 | تم رفض الطلب | Request Rejected | 4 |
| 5 | جاري العمل | In Progress | 5 |
| 7 | تم انشاء الفاتوره | Invoice Created | 6 |
| 8 | تم قبول الفاتوره | Invoice Accepted | 7 |
| 9 | تم رفض الفاتوره | Invoice Rejected | 8 |
| 3 | تم الحل | Resolved | 9 |
| 4 | تم الألغاء | Canceled | 10 |
| 50 | اعادة جدولة | Rescheduled | 11 |

### Visitor Access Statuses (IDs 11-17)
| ID | Arabic | English |
|----|--------|---------|
| 11 | جديد | New |
| 12 | في الانتظار | Pending |
| 13 | موافق عليه | Approved |
| 14 | مرفوض | Rejected |
| 15 | ألغي | Canceled |
| 16 | تم تسجيل الدخول | Checked In |
| 17 | تم تسجيل الخروج | Checked Out |

### Facility Booking Statuses (IDs 19-22, 35-38)
| ID | Arabic | English |
|----|--------|---------|
| 19 | في انتظار الموافقة | Pending Approval |
| 20 | تم الحجز | Booked |
| 21 | تم رفض الحجز | Booking Rejected |
| 22 | تم الألغاء | Canceled |
| 35 | مجدول | Scheduled |
| 36 | مكتمل | Completed |
| 37 | ملغى | Canceled |
| 38 | مرفوض | Rejected |

### Marketplace/Sales Statuses (IDs 39-49, 62-69)
| ID | Arabic | English |
|----|--------|---------|
| 39 | تم انشاء الحجز الأولي | Initial Booking Created |
| 40 | تمت الموافقة | Approved |
| 41 | الغاء قبل العربون | Canceled Before Deposit |
| 42 | تم رفض الحجز | Booking Rejected |
| 43 | الغاء بعد العربون | Canceled After Deposit |
| 44 | تم دفع العربون | Deposit Paid |
| 45 | تم إرسال العقد | Contract Sent |
| 46 | اكتمل الدفع | Payment Complete |
| 47 | تم توقيع العقد | Contract Signed |
| 48 | تم نقل الملكية | Ownership Transferred |
| 49 | تم الغاء العقد | Contract Canceled |
| 62 | مراجعة البيانات المالية | Financial Review |
| 63 | جدول دفع الوحدة | Unit Payment Schedule |
| 64 | دفع عمولة ضريبة القيمة المضافة | VAT Commission Payment |
| 65 | في انتظار الارسال | Pending Sending |
| 66 | تم الدفع | Paid |
| 67 | تم الارسال | Sent |
| 68 | في الانتظار | Pending |
| 69 | تم اكتمال | Completed |

---

## Workflow: Lease Lifecycle

```
┌─────────────────────────────────────────────────────────────────┐
│                      LEASE LIFECYCLE                            │
└─────────────────────────────────────────────────────────────────┘

    ┌─────────────┐
    │ New Contract│ (30)
    │  عقد جديد   │
    └──────┬──────┘
           │
           │ activate
           ▼
    ┌─────────────┐
    │   Active    │ (31)
    │ عقد ساري   │◄──────────┐
    └──────┬──────┘           │
           │                   │ renew
           │                   │
    ┌──────┼──────────────────┼──────┐
    │      │                   │      │
    │      ▼                   │      │
    │ ┌─────────┐              │      │
    │ │ Expired │ (32)         │      │
    │ │عقد منتهي│─────────────┘      │
    │ └─────────┘  (if renewed)       │
    │                                  │
    └──────┬───────────────────────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌─────────┐  ┌─────────┐
│Canceled │  │ Closed  │ (34)
│ عقد ملغي│  │ عقد مغلق│
│  (33)   │  └─────────┘
└─────────┘   (move-out)
 (terminate)
```

### Lease API Endpoints

| Action | Endpoint | Status Transition |
|--------|----------|-------------------|
| Create | `POST /rf/leases` | → New (30) |
| Activate | (automatic on start_date) | 30 → 31 |
| Renew | `POST /rf/leases/renew/store` | 31/32 → 31 (new lease) |
| Terminate | `POST /rf/leases/change-status/terminate` | 31 → 33 |
| Move Out | `POST /rf/leases/change-status/move-out` | 31 → 34 |
| Expire | (automatic on end_date) | 31 → 32 |

---

## Workflow: Service Request Lifecycle

```
┌─────────────────────────────────────────────────────────────────┐
│                  SERVICE REQUEST LIFECYCLE                       │
└─────────────────────────────────────────────────────────────────┘

         ┌─────────┐
         │   New   │ (1/52)
         │  جديد   │
         └────┬────┘
              │
       ┌──────┴──────┐
       │             │
       ▼             ▼
┌───────────┐  ┌───────────┐
│ Assigned  │  │ Rejected  │ (10/54)
│ تم التعيين│  │ مرفوض     │
│    (2)    │  └───────────┘
└─────┬─────┘
      │
      ▼
┌───────────┐
│ Accepted  │ (6)
│تم قبول    │
└─────┬─────┘
      │
      ├──────────────────────────────┐
      │                              │
      ▼                              ▼
┌───────────┐                 ┌───────────┐
│In Progress│ (5)             │Quote Sent │ (55)
│جاري العمل │                 │عرض السعر  │
└─────┬─────┘                 └─────┬─────┘
      │                              │
      │                       ┌──────┴──────┐
      │                       │             │
      │                       ▼             ▼
      │                 ┌─────────┐   ┌─────────┐
      │                 │Accepted │   │Rejected │
      │                 │من العميل│   │من العميل│
      │                 │  (57)   │   │  (56)   │
      │                 └────┬────┘   └─────────┘
      │                      │
      └──────────────────────┤
                             ▼
                      ┌───────────┐
                      │  Invoice  │ (7)
                      │ Created   │
                      │تم انشاء   │
                      └─────┬─────┘
                            │
                     ┌──────┴──────┐
                     │             │
                     ▼             ▼
              ┌───────────┐  ┌───────────┐
              │ Accepted  │  │ Rejected  │
              │تم قبول    │  │تم رفض     │
              │الفاتوره(8)│  │الفاتوره(9)│
              └─────┬─────┘  └───────────┘
                    │
                    ▼
              ┌───────────┐
              │ Resolved  │ (3)
              │  تم الحل  │
              └───────────┘
```

### Cancel/Reschedule Paths
- From any state → **Canceled** (4) - `POST /rf/requests/change-status/canceled`
- From In Progress → **Rescheduled** (50) - `allow_professional_reschedule` permission

---

## Workflow: Visitor Access

```
┌─────────────────────────────────────────────────────────────────┐
│                    VISITOR ACCESS LIFECYCLE                      │
└─────────────────────────────────────────────────────────────────┘

         ┌─────────┐
         │   New   │ (11)
         │  جديد   │
         └────┬────┘
              │
              ▼
         ┌─────────┐
         │ Pending │ (12)
         │في الانتظار│
         └────┬────┘
              │
       ┌──────┴──────┐
       │             │
       ▼             ▼
┌───────────┐  ┌───────────┐
│ Approved  │  │ Rejected  │ (14)
│ موافق عليه│  │ مرفوض     │
│   (13)    │  └───────────┘
└─────┬─────┘
      │
      ▼
┌───────────┐
│Checked In │ (16)
│تم الدخول  │
└─────┬─────┘
      │
      ▼
┌───────────┐
│Checked Out│ (17)
│تم الخروج  │
└───────────┘

┌──────────────────────────────────────────┐
│ Cancel path: Any state → Canceled (15)   │
└──────────────────────────────────────────┘
```

---

## Workflow: Facility Booking

```
┌─────────────────────────────────────────────────────────────────┐
│                   FACILITY BOOKING LIFECYCLE                     │
└─────────────────────────────────────────────────────────────────┘

         ┌─────────────────┐
         │ Pending Approval│ (19)
         │في انتظار الموافقة│
         └────────┬────────┘
                  │
           ┌──────┴──────┐
           │             │
           ▼             ▼
    ┌───────────┐  ┌───────────┐
    │  Booked   │  │ Rejected  │ (21/38)
    │ تم الحجز  │  │ تم رفض    │
    │   (20)    │  └───────────┘
    └─────┬─────┘
          │
          ▼
    ┌───────────┐
    │ Scheduled │ (35)
    │  مجدول    │
    └─────┬─────┘
          │
          ▼
    ┌───────────┐
    │ Completed │ (36)
    │  مكتمل    │
    └───────────┘

┌──────────────────────────────────────────┐
│ Cancel path: Any state → Canceled (22/37)│
└──────────────────────────────────────────┘
```

---

## Workflow: Marketplace Sales

```
┌─────────────────────────────────────────────────────────────────┐
│                   MARKETPLACE SALES LIFECYCLE                    │
└─────────────────────────────────────────────────────────────────┘

┌───────────────────┐
│ Initial Booking   │ (39)
│تم انشاء الحجز     │
└─────────┬─────────┘
          │
   ┌──────┴──────────────────────┐
   │                              │
   ▼                              ▼
┌───────────┐              ┌─────────────┐
│ Approved  │ (40)         │Cancel Before│ (41)
│تمت الموافقة│              │  Deposit    │
└─────┬─────┘              │الغاء قبل    │
      │                    │العربون      │
      │                    └─────────────┘
      ▼
┌───────────────┐
│ Deposit Paid  │ (44)
│ تم دفع العربون│
└───────┬───────┘
        │
        │  ┌─────────────────────┐
        │  │Cancel After Deposit │ (43)
        │  │الغاء بعد العربون    │
        │  └─────────────────────┘
        ▼
┌───────────────┐
│ Contract Sent │ (45)
│ تم إرسال العقد│
└───────┬───────┘
        │
        ▼
┌────────────────┐
│Payment Complete│ (46)
│  اكتمل الدفع   │
└───────┬────────┘
        │
        ▼
┌────────────────┐
│Contract Signed │ (47)
│ تم توقيع العقد │
└───────┬────────┘
        │
        ▼
┌────────────────────┐
│Ownership Transferred│ (48)
│   تم نقل الملكية    │
└────────────────────┘

┌──────────────────────────────────────────┐
│ Cancel: Any state → Contract Canceled(49)│
│ Reject: → Booking Rejected (42)          │
└──────────────────────────────────────────┘
```

---

## Unit Status Transitions

```
┌─────────────────────────────────────────────────────────────────┐
│                     UNIT STATUS TRANSITIONS                      │
└─────────────────────────────────────────────────────────────────┘

                    ┌───────────┐
          ┌────────►│ Available │◄────────┐
          │         │  متاحة(26)│         │
          │         └─────┬─────┘         │
          │               │               │
          │        ┌──────┴──────┐        │
          │        │             │        │
  lease   │        ▼             ▼        │ move-out/
  ends    │  ┌───────────┐ ┌───────────┐  │ cancel
          │  │  Rented   │ │   Sold    │  │
          │  │ مؤجرة(25) │ │ مباعة(23) │  │
          │  └─────┬─────┘ └─────┬─────┘  │
          │        │             │        │
          │        └──────┬──────┘        │
          │               │               │
          │               ▼               │
          │      ┌────────────────┐       │
          └──────│ Sold & Rented  │───────┘
                 │مباعة و مؤجرة(24)│
                 └────────────────┘
```

---

## Key Business Rules

### Lease Rules
1. `autoGenerateLeaseNumber` - System can auto-generate lease numbers
2. `handover_date` - Required for new leases
3. `tenant.national_id` - Required for tenant verification
4. Lease renewal creates a new lease record linked to the original

### Request Rules
1. Service managers only see requests for their assigned service types
2. `attachments_required` - Some categories require photo documentation
3. `allow_professional_reschedule` - Controls who can reschedule
4. Invoice must be created before request can be resolved

### Marketplace Rules
1. Deposit must be paid before contract is sent
2. `bank_contract_signing_days` - Time limit for bank sales
3. `cash_contract_signing_days` - Time limit for cash sales
4. `deposit_time_limit_days` - Time limit to pay deposit

---

## Related API Endpoints

| Workflow | List | Create | Update Status |
|----------|------|--------|---------------|
| Lease | `GET /rf/leases` | `POST /rf/leases` | `POST /rf/leases/change-status/*` |
| Request | `GET /rf/requests` | `POST /rf/requests` | `POST /rf/requests/change-status/*` |
| Visitor | `GET /rf/visitors` | `POST /rf/visitors` | - |
| Facility | `GET /rf/facilities` | `POST /rf/facilities` | - |
| Marketplace | `GET /marketplace/admin/units` | - | - |
