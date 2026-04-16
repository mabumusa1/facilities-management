# PRD-021: Company Profile Settings

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M0 - Foundation |
| **Estimated Effort** | 1 week |
| **Dependencies** | None |
| **Related Pages** | settings-company-*, company-profile-* |

## Problem Statement

Property management companies need to configure their business profile including company details, branding, contact information, and operational settings that appear on documents and communications.

## Goals

1. Configure company information
2. Upload branding assets (logo, colors)
3. Set up contact details
4. Configure regional settings
5. Manage business documents

## User Stories

### US-001: Configure Company Details
**As an** admin
**I want to** configure company information
**So that** it appears correctly on documents

**Acceptance Criteria:**
- Enter company name (English/Arabic)
- Enter registration number
- Enter VAT/Tax number
- Enter company address
- Set business type

### US-002: Upload Branding
**As an** admin
**I want to** upload company branding
**So that** documents look professional

**Acceptance Criteria:**
- Upload company logo
- Upload secondary logo (Arabic)
- Set primary/secondary colors
- Set font preferences
- Preview on sample document

### US-003: Contact Information
**As an** admin
**I want to** set contact information
**So that** tenants can reach us

**Acceptance Criteria:**
- Main phone number
- Support phone number
- Email addresses (support, billing)
- Physical address
- Social media links

### US-004: Regional Settings
**As an** admin
**I want to** configure regional settings
**So that** formats are correct

**Acceptance Criteria:**
- Default language
- Currency (QAR, SAR, AED, etc.)
- Date format
- Time format
- First day of week

### US-005: Legal Documents
**As an** admin
**I want to** upload legal documents
**So that** they're accessible

**Acceptance Criteria:**
- Terms and conditions
- Privacy policy
- Lease terms template
- Community rules template
- Version history

## Technical Requirements

### Database Schema

```sql
-- company_profiles table
CREATE TABLE company_profiles (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT UNIQUE REFERENCES tenants(id),

    -- Basic info
    name VARCHAR(255) NOT NULL,
    name_ar VARCHAR(255),
    legal_name VARCHAR(255),
    registration_number VARCHAR(100),
    vat_number VARCHAR(100),
    business_type VARCHAR(100),

    -- Address
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),

    -- Contact
    phone VARCHAR(50),
    support_phone VARCHAR(50),
    email VARCHAR(255),
    support_email VARCHAR(255),
    billing_email VARCHAR(255),
    website VARCHAR(500),

    -- Social
    facebook_url VARCHAR(500),
    twitter_url VARCHAR(500),
    instagram_url VARCHAR(500),
    linkedin_url VARCHAR(500),

    -- Branding
    logo_url VARCHAR(500),
    logo_ar_url VARCHAR(500),
    favicon_url VARCHAR(500),
    primary_color VARCHAR(7), -- #RRGGBB
    secondary_color VARCHAR(7),
    accent_color VARCHAR(7),

    -- Regional
    default_language ENUM('en', 'ar') DEFAULT 'en',
    currency VARCHAR(3) DEFAULT 'QAR',
    date_format VARCHAR(20) DEFAULT 'DD/MM/YYYY',
    time_format ENUM('12h', '24h') DEFAULT '12h',
    timezone VARCHAR(50) DEFAULT 'Asia/Qatar',
    first_day_of_week INT DEFAULT 0, -- 0=Sunday, 1=Monday

    -- Settings
    fiscal_year_start_month INT DEFAULT 1,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- company_documents table
CREATE TABLE company_documents (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),

    type ENUM('terms', 'privacy', 'lease_template', 'rules_template', 'other'),
    title VARCHAR(255),
    title_ar VARCHAR(255),
    description TEXT,

    file_url VARCHAR(500),
    file_type VARCHAR(50),
    file_size INT,

    version VARCHAR(20),
    is_active BOOLEAN DEFAULT true,
    published_at TIMESTAMP,

    uploaded_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/settings/company | Get company profile |
| PUT | /api/settings/company | Update company profile |
| POST | /api/settings/company/logo | Upload logo |
| DELETE | /api/settings/company/logo | Remove logo |
| GET | /api/settings/company/documents | List documents |
| POST | /api/settings/company/documents | Upload document |
| PUT | /api/settings/company/documents/{id} | Update document |
| DELETE | /api/settings/company/documents/{id} | Delete document |

### UI Components

1. **Company Info** (`/settings/company/info`)
   - Company name fields
   - Registration details
   - Address form
   - Save button

2. **Branding** (`/settings/company/branding`)
   - Logo upload with preview
   - Color pickers
   - Sample document preview

3. **Contact** (`/settings/company/contact`)
   - Phone numbers
   - Email addresses
   - Social media links

4. **Regional** (`/settings/company/regional`)
   - Language selector
   - Currency selector
   - Format preferences

5. **Documents** (`/settings/company/documents`)
   - Document list
   - Upload form
   - Version management
   - Publish/Unpublish

## Captured Page Analysis

- `settings-company-profile` - Company profile settings
- `settings-company-branding` - Branding settings
- `company-profile-*` - Various profile pages

## Validation Rules

| Field | Rules |
|-------|-------|
| name | Required, max:255 |
| email | Required, valid email |
| phone | Valid phone format |
| vat_number | Valid format per country |
| logo | Image, max:2MB, jpg/png |
| primary_color | Valid hex color |

## Testing Requirements

1. **Unit Tests** - Validation, formatting
2. **Feature Tests** - Profile CRUD, file uploads
3. **E2E Tests** - Update profile, preview documents

## References

- Captured Pages: `docs/pages/settings-company-*`, `docs/pages/company-profile-*`
