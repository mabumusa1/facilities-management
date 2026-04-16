# PRD-025: Booking Contracts

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Medium |
| **Milestone** | M4 - Operations |
| **Estimated Effort** | 1-2 weeks |
| **Dependencies** | PRD-009 (Facilities Booking), PRD-002 (Contract Types) |
| **Related Pages** | booking-contracts-*, facility-contract-* |

## Problem Statement

Facility bookings (especially long-term or high-value) require formal contracts. Tenants need to sign booking agreements that outline terms, liability, deposit requirements, and rules.

## Goals

1. Generate booking contracts from templates
2. Support digital signatures
3. Track contract status
4. Manage deposits per contract
5. Handle contract renewals

## User Stories

### US-001: Generate Booking Contract
**As a** property manager
**I want to** generate a contract for a booking
**So that** terms are formally documented

**Acceptance Criteria:**
- Auto-generate from template
- Pre-fill booking details
- Include terms and conditions
- Calculate deposit amount
- Preview before sending

### US-002: Send for Signature
**As a** property manager
**I want to** send contract for signature
**So that** tenant can sign digitally

**Acceptance Criteria:**
- Email contract to tenant
- Mobile-friendly signing
- OTP verification
- Timestamp signature
- Send confirmation

### US-003: Track Contract Status
**As a** property manager
**I want to** track contract status
**So that** I know which are pending

**Acceptance Criteria:**
- Draft/Sent/Signed/Expired status
- Reminder for pending
- Expiry warnings
- Countersignature support

### US-004: Manage Contract Deposits
**As a** property manager
**I want to** manage booking deposits
**So that** they're properly tracked

**Acceptance Criteria:**
- Set deposit amount
- Record deposit payment
- Link to transaction
- Release/forfeit deposit
- Deposit history

### US-005: Renew Contract
**As a** property manager
**I want to** renew booking contracts
**So that** ongoing bookings continue

**Acceptance Criteria:**
- Create renewal from existing
- Adjust terms if needed
- Carry over deposit
- Update linked booking

## Technical Requirements

### Database Schema

```sql
-- booking_contracts table
CREATE TABLE booking_contracts (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    contract_number VARCHAR(50) UNIQUE,

    -- Linked entities
    booking_id BIGINT REFERENCES facility_bookings(id),
    facility_id BIGINT REFERENCES facilities(id),
    contact_id BIGINT REFERENCES contacts(id),

    -- Contract details
    template_id BIGINT REFERENCES booking_contract_templates(id),
    title VARCHAR(255),
    description TEXT,

    -- Terms
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    terms_text TEXT,
    special_conditions TEXT,

    -- Financials
    booking_fee DECIMAL(10,2),
    deposit_amount DECIMAL(10,2),
    deposit_status ENUM('pending', 'paid', 'released', 'forfeited') DEFAULT 'pending',
    deposit_transaction_id BIGINT REFERENCES transactions(id),

    -- Status
    status ENUM('draft', 'sent', 'viewed', 'signed', 'countersigned', 'active', 'completed', 'cancelled', 'expired') DEFAULT 'draft',

    -- Signature
    signed_at TIMESTAMP,
    signed_ip VARCHAR(45),
    signature_data TEXT, -- base64 or reference
    signer_name VARCHAR(255),
    signer_email VARCHAR(255),
    verification_code VARCHAR(10),
    verified_at TIMESTAMP,

    -- Counter-signature (for manager)
    countersigned_by BIGINT REFERENCES users(id),
    countersigned_at TIMESTAMP,

    -- Document
    document_url VARCHAR(500), -- generated PDF
    signed_document_url VARCHAR(500),

    -- Renewal
    original_contract_id BIGINT REFERENCES booking_contracts(id),
    renewal_count INT DEFAULT 0,

    sent_at TIMESTAMP,
    sent_by BIGINT REFERENCES users(id),
    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- booking_contract_templates table
CREATE TABLE booking_contract_templates (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),

    name VARCHAR(255) NOT NULL,
    description TEXT,

    -- Template content
    content TEXT NOT NULL, -- HTML with placeholders
    css_styles TEXT,

    -- Settings
    requires_deposit BOOLEAN DEFAULT false,
    default_deposit_amount DECIMAL(10,2),
    deposit_calculation ENUM('fixed', 'percentage', 'per_day') DEFAULT 'fixed',
    deposit_percentage DECIMAL(5,2),
    deposit_per_day DECIMAL(10,2),

    requires_countersign BOOLEAN DEFAULT false,
    validity_days INT DEFAULT 7, -- days before unsigned contract expires

    is_default BOOLEAN DEFAULT false,
    is_active BOOLEAN DEFAULT true,

    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- contract_activities table
CREATE TABLE contract_activities (
    id BIGINT PRIMARY KEY,
    contract_id BIGINT REFERENCES booking_contracts(id),

    activity_type ENUM('created', 'sent', 'viewed', 'signed', 'countersigned', 'deposit_paid', 'deposit_released', 'cancelled', 'renewed'),
    description TEXT,

    performed_by BIGINT REFERENCES users(id),
    ip_address VARCHAR(45),
    created_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/booking-contracts | List contracts |
| POST | /api/booking-contracts | Create contract |
| GET | /api/booking-contracts/{id} | Get contract |
| PUT | /api/booking-contracts/{id} | Update contract |
| POST | /api/booking-contracts/{id}/send | Send for signature |
| POST | /api/booking-contracts/{id}/resend | Resend notification |
| GET | /api/booking-contracts/{id}/view | Public view (for signing) |
| POST | /api/booking-contracts/{id}/sign | Submit signature |
| POST | /api/booking-contracts/{id}/verify | Verify OTP |
| POST | /api/booking-contracts/{id}/countersign | Counter-sign |
| POST | /api/booking-contracts/{id}/cancel | Cancel contract |
| POST | /api/booking-contracts/{id}/renew | Create renewal |
| POST | /api/booking-contracts/{id}/deposit/pay | Record deposit |
| POST | /api/booking-contracts/{id}/deposit/release | Release deposit |
| POST | /api/booking-contracts/{id}/deposit/forfeit | Forfeit deposit |
| GET | /api/booking-contracts/templates | List templates |
| POST | /api/booking-contracts/templates | Create template |
| PUT | /api/booking-contracts/templates/{id} | Update template |

### Template Placeholders

```php
$placeholders = [
    'contract' => [
        '{{contract.number}}',
        '{{contract.date}}',
        '{{contract.start_date}}',
        '{{contract.end_date}}',
    ],
    'facility' => [
        '{{facility.name}}',
        '{{facility.location}}',
        '{{facility.type}}',
    ],
    'booking' => [
        '{{booking.reference}}',
        '{{booking.date}}',
        '{{booking.time}}',
        '{{booking.duration}}',
        '{{booking.fee}}',
    ],
    'tenant' => [
        '{{tenant.name}}',
        '{{tenant.email}}',
        '{{tenant.phone}}',
        '{{tenant.unit}}',
    ],
    'deposit' => [
        '{{deposit.amount}}',
        '{{deposit.due_date}}',
    ],
    'company' => [
        '{{company.name}}',
        '{{company.address}}',
        '{{company.phone}}',
        '{{company.logo}}',
    ],
];
```

### Signature Flow

```javascript
// Client-side signature capture
const signatureFlow = {
  steps: [
    {
      name: 'review',
      description: 'Review contract terms',
      action: 'acknowledge',
    },
    {
      name: 'identity',
      description: 'Enter full name as signature',
      input: 'text',
    },
    {
      name: 'draw',
      description: 'Draw signature',
      input: 'canvas', // optional
    },
    {
      name: 'verify',
      description: 'Verify with OTP',
      input: 'otp',
    },
    {
      name: 'confirm',
      description: 'Final confirmation',
      action: 'submit',
    },
  ],
};
```

### UI Components

1. **Contracts List** (`/booking-contracts`)
   - Contract cards/table
   - Status filters
   - Search
   - Create button

2. **Create/Edit Contract**
   - Template selector
   - Booking selector
   - Terms editor
   - Deposit configuration
   - Preview

3. **Contract Preview**
   - Full document view
   - Download PDF
   - Send button
   - Sign button (for signers)

4. **Signing Page** (public)
   - Contract display
   - Signature pad
   - OTP input
   - Submit button

5. **Template Manager**
   - Template list
   - Rich text editor
   - Placeholder insertion
   - Preview

## Notifications

| Event | Recipients | Channel |
|-------|-----------|------------|
| Contract Sent | Signer | Email, SMS |
| Reminder | Signer | Email |
| Contract Signed | Manager | Email, Push |
| Contract Countersigned | Signer | Email |
| Deposit Due | Signer | Email |
| Deposit Confirmed | Signer | Email |
| Contract Expiring | Both | Email |

## Captured Page Analysis

- `booking-contracts-list` - Contract list
- `booking-contracts-details` - Contract detail
- `booking-contracts-sign` - Signing page
- `facility-contract-*` - Facility contract pages

## Testing Requirements

1. **Unit Tests** - Template rendering, deposit calculation
2. **Feature Tests** - CRUD, signing flow
3. **E2E Tests** - Create contract, send, sign, countersign
4. **Security Tests** - OTP verification, signature integrity

## References

- Captured Pages: `docs/pages/booking-contracts-*`, `docs/pages/facility-contract-*`
- Digital Signatures: eIDAS-style timestamp + IP + name
