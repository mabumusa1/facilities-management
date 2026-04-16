# PRD-012: Directory Management

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Medium |
| **Milestone** | M6 - Communication |
| **Estimated Effort** | 1-2 weeks |
| **Dependencies** | None |
| **Related Pages** | communication-directory, dashboard-directory-*, directory-*, settings-directory |

## Problem Statement

Tenants need easy access to community directory information including facilities, services, emergency contacts, and useful resources. Property managers need to maintain this directory.

## Goals

1. Maintain community directory of services
2. List available facilities with details
3. Provide emergency and important contacts
4. Share useful documents and resources
5. Enable search and categorization

## User Stories

### US-001: Manage Directory Categories
**As a** property manager
**I want to** organize directory into categories
**So that** information is easy to find

**Acceptance Criteria:**
- Create categories (Facilities, Services, Contacts, Documents)
- Add icons and descriptions
- Set display order
- Enable/disable categories

### US-002: Add Directory Entry
**As a** property manager
**I want to** add directory entries
**So that** tenants have useful information

**Acceptance Criteria:**
- Select category
- Enter name and description
- Add contact details (phone, email, website)
- Add location/address
- Upload images
- Set operating hours

### US-003: View Directory
**As a** tenant
**I want to** browse the directory
**So that** I can find what I need

**Acceptance Criteria:**
- Browse by category
- Search entries
- View entry details
- Call/email directly
- Get directions

### US-004: Manage Facilities in Directory
**As a** property manager
**I want to** link facilities to directory
**So that** tenants know what's available

**Acceptance Criteria:**
- Auto-populate from facilities
- Add additional details
- Link to booking

### US-005: Share Documents
**As a** property manager
**I want to** share documents via directory
**So that** tenants can access resources

**Acceptance Criteria:**
- Upload PDF/documents
- Categorize documents
- Set access permissions
- Track downloads

## Technical Requirements

### Database Schema

```sql
-- directory_categories table
CREATE TABLE directory_categories (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    name VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100),
    description TEXT,
    icon VARCHAR(50),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- directory_entries table
CREATE TABLE directory_entries (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    category_id BIGINT REFERENCES directory_categories(id),
    community_id BIGINT REFERENCES communities(id),

    name VARCHAR(255) NOT NULL,
    name_ar VARCHAR(255),
    description TEXT,
    description_ar TEXT,

    -- Contact info
    phone VARCHAR(50),
    email VARCHAR(255),
    website VARCHAR(500),
    address TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),

    -- Media
    images JSON,
    logo_url VARCHAR(500),

    -- Hours
    operating_hours JSON, -- {mon: {open, close}, ...}

    -- Linking
    facility_id BIGINT REFERENCES facilities(id),

    is_featured BOOLEAN DEFAULT false,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- directory_documents table
CREATE TABLE directory_documents (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    category_id BIGINT REFERENCES directory_categories(id),
    community_id BIGINT REFERENCES communities(id),

    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_url VARCHAR(500) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,

    is_public BOOLEAN DEFAULT true,
    download_count INT DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/directory/categories | List categories |
| POST | /api/directory/categories | Create category |
| GET | /api/directory/entries | List entries |
| POST | /api/directory/entries | Create entry |
| GET | /api/directory/entries/{id} | Get entry |
| PUT | /api/directory/entries/{id} | Update entry |
| DELETE | /api/directory/entries/{id} | Delete entry |
| GET | /api/directory/documents | List documents |
| POST | /api/directory/documents | Upload document |
| GET | /api/directory/search | Search directory |

### UI Components

1. **Directory Main** (`/directory`)
   - Category cards
   - Search bar
   - Featured entries

2. **Category View**
   - Entry list
   - Filter/sort options
   - Map view toggle

3. **Entry Detail**
   - Full information
   - Contact buttons
   - Map location
   - Operating hours

4. **Manager Dashboard** (`/dashboard/directory`)
   - Entry management
   - Document upload
   - Category settings

5. **Settings** (`/settings/directory`)
   - Category management
   - Default entries

## Captured Page Analysis

- `communication-directory` - Directory main
- `dashboard-directory` - Manager view
- `dashboard-directory-create` - Create entry
- `dashboard-directory-details` - Entry detail
- `directory-*` - Various directory pages
- `settings-directory` - Directory settings

## Testing Requirements

1. **Unit Tests** - Search, filtering
2. **Feature Tests** - CRUD operations
3. **E2E Tests** - Browse directory, download document

## References

- Captured Pages: `docs/pages/communication-directory`, `docs/pages/dashboard-directory-*`, `docs/pages/directory-*`
