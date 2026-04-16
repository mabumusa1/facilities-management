# PRD-006: Transaction Recording

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M4 - Financial |
| **Estimated Effort** | 2-3 weeks |
| **Dependencies** | Leases, Contacts |
| **Related Pages** | transactions-*, settings-transaction-schedules |

## Problem Statement

Property managers need to record financial transactions including rent payments, expenses, refunds, and other money movements. Currently, only transaction listing exists. Full recording, categorization, and reporting capabilities are needed.

## Goals

1. Record money-in transactions (rent, deposits, fees)
2. Record money-out transactions (expenses, refunds, payments)
3. Support recurring transaction schedules
4. Generate invoices and receipts
5. Track overdue payments
6. Integrate with chart of accounts

## User Stories

### US-001: Record Money-In Transaction
**As a** property manager
**I want to** record a money-in transaction
**So that** I can track income

**Acceptance Criteria:**
- Select transaction type (rent, deposit, fee, other)
- Select tenant/contact
- Select related unit/lease
- Enter amount and date
- Choose payment method
- Add reference/receipt number
- Attach supporting documents
- Auto-generate receipt

### US-002: Record Money-Out Transaction
**As a** property manager
**I want to** record a money-out transaction
**So that** I can track expenses

**Acceptance Criteria:**
- Select transaction type (maintenance, utility, refund, other)
- Select vendor/contact
- Select related unit/property
- Enter amount and date
- Choose payment method
- Add reference/invoice number
- Attach supporting documents
- Categorize expense

### US-003: Transaction Schedules
**As a** property manager
**I want to** set up recurring transactions
**So that** rent is automatically recorded each month

**Acceptance Criteria:**
- Create schedule from lease
- Set frequency (monthly, quarterly, yearly)
- Set start and end dates
- Configure auto-generation date
- Enable/disable notifications
- Handle schedule changes

### US-004: Overdue Tracking
**As a** property manager
**I want to** track overdue payments
**So that** I can follow up with tenants

**Acceptance Criteria:**
- Dashboard widget for overdues
- List of overdue transactions
- Filter by days overdue
- Send reminder notifications
- Track follow-up actions
- Apply late fees

### US-005: Chart of Accounts
**As a** property manager
**I want to** categorize transactions by account
**So that** I can generate financial reports

**Acceptance Criteria:**
- Pre-defined chart of accounts
- Custom account creation
- Map categories to accounts
- Account balances
- Export for accounting software

### US-006: Generate Invoice
**As a** property manager
**I want to** generate invoices
**So that** tenants know what they owe

**Acceptance Criteria:**
- Generate from scheduled transaction
- Manual invoice creation
- Company branding
- Line items with details
- PDF export
- Email to tenant

### US-007: Journal Entries
**As a** property manager
**I want to** record journal entries
**So that** I can make accounting adjustments

**Acceptance Criteria:**
- Create debit/credit entries
- Balance validation
- Entry description
- Reference documents
- Audit trail

## Technical Requirements

### Database Schema

```sql
-- transactions table (exists, enhance)
ALTER TABLE transactions ADD COLUMN (
    direction ENUM('in', 'out') NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'check', 'card', 'online'),
    payment_reference VARCHAR(100),
    receipt_number VARCHAR(50),
    invoice_id BIGINT REFERENCES invoices(id),
    schedule_id BIGINT REFERENCES transaction_schedules(id),
    due_date DATE,
    paid_date DATE,
    is_overdue BOOLEAN DEFAULT false,
    days_overdue INT DEFAULT 0,
    account_id BIGINT REFERENCES chart_of_accounts(id),
    attachments JSON
);

-- transaction_schedules table
CREATE TABLE transaction_schedules (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    lease_id BIGINT REFERENCES leases(id),
    contact_id BIGINT REFERENCES contacts(id),
    unit_id BIGINT REFERENCES units(id),

    -- Schedule details
    name VARCHAR(255),
    amount DECIMAL(10,2),
    transaction_type_id BIGINT REFERENCES transaction_types(id),
    category_id BIGINT REFERENCES transaction_categories(id),

    -- Frequency
    frequency ENUM('daily', 'weekly', 'monthly', 'quarterly', 'yearly'),
    day_of_month INT, -- 1-28 for monthly
    month_of_year INT, -- 1-12 for yearly

    -- Duration
    start_date DATE,
    end_date DATE,
    next_generation_date DATE,

    -- Settings
    generate_days_before INT DEFAULT 7,
    auto_send_reminder BOOLEAN DEFAULT true,
    reminder_days_before INT DEFAULT 3,

    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- chart_of_accounts table
CREATE TABLE chart_of_accounts (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    code VARCHAR(20) UNIQUE,
    name VARCHAR(255),
    type ENUM('asset', 'liability', 'equity', 'income', 'expense'),
    parent_id BIGINT REFERENCES chart_of_accounts(id),
    is_system BOOLEAN DEFAULT false,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- invoices table
CREATE TABLE invoices (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    invoice_number VARCHAR(50) UNIQUE,
    contact_id BIGINT REFERENCES contacts(id),
    unit_id BIGINT REFERENCES units(id),
    lease_id BIGINT REFERENCES leases(id),

    -- Amounts
    subtotal DECIMAL(10,2),
    tax_amount DECIMAL(10,2),
    total_amount DECIMAL(10,2),
    paid_amount DECIMAL(10,2) DEFAULT 0,
    balance_due DECIMAL(10,2),

    -- Dates
    issue_date DATE,
    due_date DATE,
    paid_date DATE,

    -- Status
    status ENUM('draft', 'sent', 'paid', 'partial', 'overdue', 'cancelled'),

    -- Details
    line_items JSON,
    notes TEXT,
    terms TEXT,

    sent_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- journal_entries table
CREATE TABLE journal_entries (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    entry_number VARCHAR(50),
    entry_date DATE,
    description TEXT,
    reference VARCHAR(255),
    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP
);

-- journal_entry_lines table
CREATE TABLE journal_entry_lines (
    id BIGINT PRIMARY KEY,
    journal_entry_id BIGINT REFERENCES journal_entries(id),
    account_id BIGINT REFERENCES chart_of_accounts(id),
    debit DECIMAL(10,2) DEFAULT 0,
    credit DECIMAL(10,2) DEFAULT 0,
    description TEXT
);
```

### API Endpoints

**Transactions:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/transactions | List transactions |
| POST | /api/transactions | Create transaction |
| GET | /api/transactions/{id} | Get transaction |
| PUT | /api/transactions/{id} | Update transaction |
| DELETE | /api/transactions/{id} | Delete transaction |
| GET | /api/transactions/overdues | List overdues |
| POST | /api/transactions/{id}/apply-late-fee | Apply late fee |

**Schedules:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/transaction-schedules | List schedules |
| POST | /api/transaction-schedules | Create schedule |
| PUT | /api/transaction-schedules/{id} | Update schedule |
| DELETE | /api/transaction-schedules/{id} | Delete schedule |
| POST | /api/transaction-schedules/{id}/generate | Generate transaction |

**Invoices:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/invoices | List invoices |
| POST | /api/invoices | Create invoice |
| GET | /api/invoices/{id} | Get invoice |
| PUT | /api/invoices/{id} | Update invoice |
| POST | /api/invoices/{id}/send | Send invoice |
| GET | /api/invoices/{id}/pdf | Download PDF |

**Chart of Accounts:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/chart-of-accounts | List accounts |
| POST | /api/chart-of-accounts | Create account |
| PUT | /api/chart-of-accounts/{id} | Update account |
| GET | /api/chart-of-accounts/balances | Get balances |

### UI Components

1. **Transactions List** (`/transactions`)
   - Tabs: All, Money In, Money Out
   - Filter by date, type, status
   - Search by reference
   - Record transaction button

2. **Record Transaction** (`/transactions/record`)
   - Direction toggle (In/Out)
   - Type selector
   - Amount and date
   - Contact selector
   - Unit/Lease selector
   - Payment method
   - Reference fields
   - Document upload

3. **Transaction Detail** (`/transactions/{id}`)
   - Full transaction info
   - Related lease/unit
   - Payment history
   - Attachments
   - Edit/Delete actions

4. **Overdues Dashboard** (`/transactions/overdues`)
   - Overdue summary cards
   - Tenant list with amounts
   - Quick actions (remind, apply fee)
   - Aging breakdown

5. **Schedules Page** (`/settings/transaction-schedules`)
   - List of schedules
   - Status indicators
   - Next generation date
   - Quick actions

6. **Chart of Accounts** (`/transactions/chart-of-accounts`)
   - Hierarchical account list
   - Account balances
   - Add account form
   - Export button

7. **Journal Entries** (`/transactions/journal-entries`)
   - Entry list
   - Create entry form
   - Debit/credit balance check

## Captured Page Analysis

### transactions (Main List)
- Tab navigation (All, In, Out)
- Date range filter
- Transaction table
- Record button

### transactions-record-transaction (Record Form)
- Type selection
- Amount input
- Contact/Unit selection
- Payment details

### transactions-overdues (Overdues)
- Overdue summary
- Tenant list
- Days overdue column
- Action buttons

### transactions-chart-of-accounts (Accounts)
- Account tree
- Balance column
- Add account modal

### transactions-journal-entries (Journal)
- Entry list
- Create entry button
- Debit/Credit columns

## Validation Rules

| Field | Rules |
|-------|-------|
| amount | Required, numeric, > 0 |
| transaction_date | Required, <= today |
| contact_id | Required |
| transaction_type_id | Required |
| payment_method | Required |
| journal_entry lines | Sum of debits must equal credits |

## Testing Requirements

1. **Unit Tests**
   - Transaction creation
   - Schedule generation
   - Overdue calculation
   - Journal entry balancing

2. **Feature Tests**
   - Record money in/out
   - Schedule lifecycle
   - Invoice generation
   - Late fee application

3. **E2E Tests**
   - Full payment recording
   - Generate and pay invoice
   - Overdue notification flow

## References

- Captured Pages: `docs/pages/transactions-*`
- API Spec: `docs/api/queries/transactions/`
- Settings: `docs/pages/settings-transaction-schedules`
