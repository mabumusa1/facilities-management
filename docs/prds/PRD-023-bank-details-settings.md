# PRD-023: Bank Details Settings

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M3 - Financial |
| **Estimated Effort** | 3-5 days |
| **Dependencies** | PRD-021 (Company Profile) |
| **Related Pages** | settings-bank-*, bank-details-* |

## Problem Statement

Property managers need to configure bank account details for receiving payments. Multiple accounts may be needed for different communities or purposes (rent, maintenance, deposits).

## Goals

1. Configure multiple bank accounts
2. Link accounts to communities/purposes
3. Display bank details on invoices
4. Support multiple currencies
5. Validate IBAN/account numbers

## User Stories

### US-001: Add Bank Account
**As an** admin
**I want to** add bank account details
**So that** tenants can make payments

**Acceptance Criteria:**
- Enter bank name
- Enter account holder name
- Enter account number
- Enter IBAN (with validation)
- Enter SWIFT/BIC code
- Set currency
- Mark as primary

### US-002: Multiple Accounts
**As an** admin
**I want to** manage multiple accounts
**So that** different communities use different banks

**Acceptance Criteria:**
- Add multiple accounts
- Assign to communities
- Set default per community
- Assign by purpose (rent, deposit)

### US-003: Display on Documents
**As an** admin
**I want to** control bank details display
**So that** they appear correctly on invoices

**Acceptance Criteria:**
- Select which details to show
- Format for display
- Show on specific document types
- QR code for payment (optional)

### US-004: Validate Account Details
**As a** system
**I want to** validate account details
**So that** payment info is accurate

**Acceptance Criteria:**
- Validate IBAN format
- Validate SWIFT code
- Check country-specific formats
- Show validation errors

## Technical Requirements

### Database Schema

```sql
-- bank_accounts table
CREATE TABLE bank_accounts (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),

    -- Bank details
    bank_name VARCHAR(255) NOT NULL,
    bank_name_ar VARCHAR(255),
    bank_code VARCHAR(20),
    branch_name VARCHAR(255),
    branch_code VARCHAR(20),

    -- Account details
    account_name VARCHAR(255) NOT NULL,
    account_name_ar VARCHAR(255),
    account_number VARCHAR(50) NOT NULL,
    iban VARCHAR(34),
    swift_code VARCHAR(11),

    -- Settings
    currency VARCHAR(3) DEFAULT 'QAR',
    is_primary BOOLEAN DEFAULT false,
    is_active BOOLEAN DEFAULT true,

    -- Purpose
    purpose ENUM('all', 'rent', 'deposit', 'maintenance', 'other') DEFAULT 'all',

    -- Display
    show_on_invoice BOOLEAN DEFAULT true,
    display_fields JSON, -- ['bank_name', 'account_name', 'iban', 'swift']

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- bank_account_communities (many-to-many)
CREATE TABLE bank_account_communities (
    id BIGINT PRIMARY KEY,
    bank_account_id BIGINT REFERENCES bank_accounts(id),
    community_id BIGINT REFERENCES communities(id),
    is_default BOOLEAN DEFAULT false,

    UNIQUE(bank_account_id, community_id)
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/settings/bank-accounts | List bank accounts |
| POST | /api/settings/bank-accounts | Add bank account |
| GET | /api/settings/bank-accounts/{id} | Get account details |
| PUT | /api/settings/bank-accounts/{id} | Update account |
| DELETE | /api/settings/bank-accounts/{id} | Delete account |
| POST | /api/settings/bank-accounts/{id}/primary | Set as primary |
| POST | /api/settings/bank-accounts/validate-iban | Validate IBAN |
| GET | /api/settings/bank-accounts/for-community/{id} | Get community accounts |

### IBAN Validation

```php
class IbanValidator
{
    // Country-specific IBAN lengths
    private const IBAN_LENGTHS = [
        'QA' => 29, // Qatar
        'SA' => 24, // Saudi Arabia
        'AE' => 23, // UAE
        'BH' => 22, // Bahrain
        'KW' => 30, // Kuwait
        'OM' => 23, // Oman
    ];

    public function validate(string $iban): bool
    {
        $iban = strtoupper(str_replace(' ', '', $iban));

        // Check country code and length
        $country = substr($iban, 0, 2);
        if (!isset(self::IBAN_LENGTHS[$country])) {
            return false;
        }
        if (strlen($iban) !== self::IBAN_LENGTHS[$country]) {
            return false;
        }

        // Checksum validation (ISO 7064 Mod 97-10)
        $rearranged = substr($iban, 4) . substr($iban, 0, 4);
        $numeric = '';
        foreach (str_split($rearranged) as $char) {
            $numeric .= ctype_digit($char) ? $char : (ord($char) - 55);
        }

        return bcmod($numeric, '97') === '1';
    }
}
```

### UI Components

1. **Bank Accounts List** (`/settings/bank-accounts`)
   - Account cards
   - Primary badge
   - Quick actions
   - Add button

2. **Add/Edit Account Form**
   - Bank details
   - Account details
   - IBAN with validation
   - Community assignment
   - Display settings

3. **IBAN Input**
   - Auto-formatting
   - Real-time validation
   - Bank logo lookup
   - Copy button

4. **Community Assignment**
   - Multi-select communities
   - Default toggle per community
   - Purpose selector

## Captured Page Analysis

- `settings-bank` - Bank settings
- `settings-bank-accounts` - Account management
- `bank-details-*` - Various bank pages

## Validation Rules

| Field | Rules |
|-------|-------|
| bank_name | Required, max:255 |
| account_name | Required, max:255 |
| account_number | Required, max:50 |
| iban | Valid IBAN format |
| swift_code | Valid SWIFT/BIC format (8 or 11 chars) |
| currency | Valid ISO 4217 code |

## Testing Requirements

1. **Unit Tests** - IBAN validation, SWIFT validation
2. **Feature Tests** - Account CRUD
3. **E2E Tests** - Add account, assign to community

## References

- Captured Pages: `docs/pages/settings-bank-*`
- IBAN Registry: https://www.swift.com/standards/data-standards/iban
