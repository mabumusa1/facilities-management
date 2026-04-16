# PRD-022: Invoice Settings

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M3 - Financial |
| **Estimated Effort** | 1 week |
| **Dependencies** | PRD-021 (Company Profile) |
| **Related Pages** | settings-invoice-*, invoice-settings-* |

## Problem Statement

Property managers need to configure invoice settings including numbering, templates, payment terms, late fees, and automated invoice generation rules.

## Goals

1. Configure invoice numbering
2. Customize invoice templates
3. Set payment terms and late fees
4. Configure automated invoice generation
5. Set up invoice reminders

## User Stories

### US-001: Configure Invoice Numbering
**As an** admin
**I want to** set invoice numbering format
**So that** invoices are numbered consistently

**Acceptance Criteria:**
- Set prefix (e.g., INV-)
- Set starting number
- Choose format (INV-2024-0001)
- Preview sample number
- Separate sequences per type

### US-002: Customize Invoice Template
**As an** admin
**I want to** customize invoice appearance
**So that** invoices match our brand

**Acceptance Criteria:**
- Choose template layout
- Set header/footer content
- Include company logo
- Add custom notes/terms
- Preview before saving

### US-003: Configure Payment Terms
**As an** admin
**I want to** set payment terms
**So that** tenants know when to pay

**Acceptance Criteria:**
- Default due days
- Grace period days
- Payment methods accepted
- Bank details on invoice
- Payment instructions

### US-004: Configure Late Fees
**As an** admin
**I want to** set late fee policies
**So that** late payments are penalized

**Acceptance Criteria:**
- Enable/disable late fees
- Fixed amount or percentage
- Grace period before fees
- Maximum fee cap
- Compound frequency

### US-005: Automated Invoice Generation
**As an** admin
**I want to** set auto-generation rules
**So that** invoices are created automatically

**Acceptance Criteria:**
- Generate on day of month
- Generate X days before due
- Auto-send to tenant
- Include reminder schedule
- Exclude specific units/leases

## Technical Requirements

### Database Schema

```sql
-- invoice_settings table
CREATE TABLE invoice_settings (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT UNIQUE REFERENCES tenants(id),

    -- Numbering
    number_prefix VARCHAR(20) DEFAULT 'INV-',
    number_format VARCHAR(50) DEFAULT '{prefix}{year}-{sequence:5}',
    next_sequence INT DEFAULT 1,
    reset_sequence_yearly BOOLEAN DEFAULT true,

    -- Template
    template_id VARCHAR(50) DEFAULT 'default',
    header_text TEXT,
    footer_text TEXT,
    terms_text TEXT,
    notes_text TEXT,
    show_logo BOOLEAN DEFAULT true,
    show_bank_details BOOLEAN DEFAULT true,

    -- Payment terms
    default_due_days INT DEFAULT 30,
    grace_period_days INT DEFAULT 0,
    payment_methods JSON, -- ['bank_transfer', 'card', 'cash']
    payment_instructions TEXT,

    -- Late fees
    late_fee_enabled BOOLEAN DEFAULT false,
    late_fee_type ENUM('fixed', 'percentage') DEFAULT 'fixed',
    late_fee_amount DECIMAL(10,2),
    late_fee_percentage DECIMAL(5,2),
    late_fee_grace_days INT DEFAULT 0,
    late_fee_cap DECIMAL(10,2),
    late_fee_compound ENUM('none', 'daily', 'weekly', 'monthly') DEFAULT 'none',

    -- Auto-generation
    auto_generate_enabled BOOLEAN DEFAULT false,
    auto_generate_day INT DEFAULT 1, -- day of month
    auto_generate_days_before_due INT,
    auto_send_enabled BOOLEAN DEFAULT false,

    -- Reminders
    reminder_enabled BOOLEAN DEFAULT false,
    reminder_days_before JSON, -- [7, 3, 1]
    reminder_days_after JSON, -- [1, 7, 14]
    reminder_template TEXT,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- invoice_templates table
CREATE TABLE invoice_templates (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),

    name VARCHAR(100),
    code VARCHAR(50),
    description TEXT,

    html_template TEXT,
    css_styles TEXT,

    is_default BOOLEAN DEFAULT false,
    is_system BOOLEAN DEFAULT false,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- invoice_number_sequences table (for multiple sequences)
CREATE TABLE invoice_number_sequences (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    sequence_key VARCHAR(50), -- 'default', 'rent', 'service', etc.
    year INT,
    next_number INT DEFAULT 1,

    UNIQUE(tenant_id, sequence_key, year)
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/settings/invoice | Get invoice settings |
| PUT | /api/settings/invoice | Update settings |
| GET | /api/settings/invoice/preview | Preview sample invoice |
| GET | /api/settings/invoice/templates | List templates |
| POST | /api/settings/invoice/templates | Create template |
| PUT | /api/settings/invoice/templates/{id} | Update template |
| DELETE | /api/settings/invoice/templates/{id} | Delete template |
| GET | /api/settings/invoice/next-number | Get next invoice number |

### Template Variables

```php
$templateVariables = [
    'invoice' => [
        'number', 'date', 'due_date', 'subtotal', 'vat', 'total',
        'amount_due', 'amount_paid', 'status',
    ],
    'company' => [
        'name', 'logo_url', 'address', 'phone', 'email',
        'vat_number', 'registration_number',
    ],
    'customer' => [
        'name', 'email', 'phone', 'address', 'unit_number',
    ],
    'items' => [
        'description', 'quantity', 'unit_price', 'amount',
    ],
    'bank' => [
        'bank_name', 'account_name', 'account_number', 'iban', 'swift',
    ],
];
```

### UI Components

1. **Invoice Settings** (`/settings/invoice`)
   - Tabbed interface
   - Numbering section
   - Payment terms section
   - Late fees section

2. **Template Editor**
   - Template selector
   - Rich text editor
   - Variable insertion
   - Live preview

3. **Auto-Generation**
   - Enable toggle
   - Schedule configuration
   - Exclusion rules
   - Test run button

4. **Reminder Configuration**
   - Enable toggle
   - Days configuration
   - Template editor
   - Preview

## Captured Page Analysis

- `settings-invoice` - Invoice settings
- `settings-invoice-templates` - Template management
- `invoice-settings-*` - Various settings pages

## Validation Rules

| Field | Rules |
|-------|-------|
| number_prefix | Max:20, alphanumeric |
| default_due_days | Integer, 1-365 |
| late_fee_amount | Numeric, >= 0 |
| late_fee_percentage | 0-100 |
| auto_generate_day | 1-28 |

## Testing Requirements

1. **Unit Tests** - Number generation, fee calculation
2. **Feature Tests** - Settings CRUD, template rendering
3. **E2E Tests** - Configure settings, generate invoice

## References

- Captured Pages: `docs/pages/settings-invoice-*`
