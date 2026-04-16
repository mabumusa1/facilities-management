# PRD-007: Transaction Schedules

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Medium |
| **Milestone** | M4 - Financial |
| **Estimated Effort** | 1 week |
| **Dependencies** | PRD-006 (Transaction Recording), Leases |
| **Related Pages** | settings-transaction-schedules |

## Problem Statement

Property managers need to automate recurring transactions (rent collection, utility charges) to reduce manual work and ensure timely billing.

## Goals

1. Create transaction schedules from leases
2. Support various frequencies (monthly, quarterly, yearly)
3. Auto-generate transactions/invoices
4. Send payment reminders
5. Handle schedule modifications

## User Stories

### US-001: Create Schedule from Lease
**As a** property manager
**I want to** auto-create payment schedule from lease
**So that** rent is billed automatically

**Acceptance Criteria:**
- Schedule created when lease activated
- Inherits rent amount and frequency
- Sets start/end from lease dates
- Links to tenant and unit

### US-002: Manual Schedule Creation
**As a** property manager
**I want to** create custom schedules
**So that** I can bill recurring charges

**Acceptance Criteria:**
- Select tenant/contact
- Set amount and frequency
- Choose transaction type
- Set schedule duration
- Configure reminder settings

### US-003: Auto-Generate Transactions
**As a** system
**I want to** auto-generate transactions
**So that** billing is consistent

**Acceptance Criteria:**
- Generate X days before due date
- Create transaction record
- Optionally create invoice
- Send notification to tenant
- Log generation event

### US-004: Modify Schedule
**As a** property manager
**I want to** modify active schedules
**So that** I can adjust for changes

**Acceptance Criteria:**
- Pause/resume schedule
- Adjust amount (effective date)
- Change frequency
- Extend/shorten duration
- View modification history

### US-005: Payment Reminders
**As a** property manager
**I want to** send payment reminders
**So that** tenants pay on time

**Acceptance Criteria:**
- Configure reminder days before due
- Auto-send reminders
- Multiple reminder levels
- Track reminder history
- Tenant can acknowledge

## Technical Requirements

### Database Schema

```sql
-- transaction_schedules table (from PRD-006, detailed here)
CREATE TABLE transaction_schedules (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    lease_id BIGINT REFERENCES leases(id),
    contact_id BIGINT REFERENCES contacts(id),
    unit_id BIGINT REFERENCES units(id),

    name VARCHAR(255),
    description TEXT,
    amount DECIMAL(10,2) NOT NULL,
    transaction_type_id BIGINT REFERENCES transaction_types(id),
    category_id BIGINT REFERENCES transaction_categories(id),

    -- Frequency
    frequency ENUM('weekly', 'biweekly', 'monthly', 'quarterly', 'yearly') NOT NULL,
    day_of_month INT, -- 1-28
    day_of_week INT, -- 0-6 for weekly

    -- Duration
    start_date DATE NOT NULL,
    end_date DATE,
    next_generation_date DATE,
    last_generated_date DATE,

    -- Generation settings
    generate_days_before INT DEFAULT 7,
    create_invoice BOOLEAN DEFAULT true,

    -- Reminders
    reminder_enabled BOOLEAN DEFAULT true,
    reminder_days JSON, -- [7, 3, 1] days before

    -- Status
    status ENUM('active', 'paused', 'completed', 'cancelled') DEFAULT 'active',

    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- schedule_modifications table
CREATE TABLE schedule_modifications (
    id BIGINT PRIMARY KEY,
    schedule_id BIGINT REFERENCES transaction_schedules(id),
    field_changed VARCHAR(50),
    old_value TEXT,
    new_value TEXT,
    effective_date DATE,
    reason TEXT,
    modified_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP
);

-- schedule_generation_log table
CREATE TABLE schedule_generation_log (
    id BIGINT PRIMARY KEY,
    schedule_id BIGINT REFERENCES transaction_schedules(id),
    transaction_id BIGINT REFERENCES transactions(id),
    invoice_id BIGINT REFERENCES invoices(id),
    generated_for_date DATE,
    generated_at TIMESTAMP,
    status ENUM('success', 'failed', 'skipped'),
    error_message TEXT
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/transaction-schedules | List schedules |
| POST | /api/transaction-schedules | Create schedule |
| GET | /api/transaction-schedules/{id} | Get schedule |
| PUT | /api/transaction-schedules/{id} | Update schedule |
| POST | /api/transaction-schedules/{id}/pause | Pause schedule |
| POST | /api/transaction-schedules/{id}/resume | Resume schedule |
| POST | /api/transaction-schedules/{id}/generate | Manual generate |
| GET | /api/transaction-schedules/{id}/history | Generation history |
| POST | /api/transaction-schedules/process | Process due schedules (cron) |

### UI Components

1. **Schedules List** (`/settings/transaction-schedules`)
   - Table with all schedules
   - Status badges
   - Next generation date
   - Quick actions

2. **Create/Edit Schedule Form**
   - Contact/Lease selector
   - Amount and frequency
   - Duration settings
   - Reminder configuration

3. **Schedule Detail**
   - Schedule info
   - Generation history
   - Modification log
   - Related transactions

## Captured Page Analysis

- `settings-transaction-schedules` - Schedule management page

## Validation Rules

| Field | Rules |
|-------|-------|
| amount | Required, numeric, > 0 |
| frequency | Required, valid enum |
| start_date | Required |
| day_of_month | Required for monthly+, 1-28 |
| generate_days_before | Min:1, Max:30 |

## Background Jobs

```php
// Daily job to process schedules
class ProcessTransactionSchedules implements ShouldQueue
{
    public function handle()
    {
        $schedules = TransactionSchedule::where('status', 'active')
            ->where('next_generation_date', '<=', now()->addDays(config('schedules.generate_days_before')))
            ->get();

        foreach ($schedules as $schedule) {
            GenerateScheduledTransaction::dispatch($schedule);
        }
    }
}
```

## Testing Requirements

1. **Unit Tests** - Date calculations, generation logic
2. **Feature Tests** - Schedule CRUD, modification history
3. **E2E Tests** - Create schedule, verify generation

## References

- Captured Pages: `docs/pages/settings-transaction-schedules`
- Transaction Recording: PRD-006
