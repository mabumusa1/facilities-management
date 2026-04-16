# PRD-017: Bulk Upload Features

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Medium |
| **Milestone** | M4 - Operations |
| **Estimated Effort** | 2 weeks |
| **Dependencies** | Properties module, Contacts module |
| **Related Pages** | upload-*, import-*, bulk-* |

## Problem Statement

Property managers need to import large datasets from existing systems - communities, buildings, units, contacts, owners. Manual entry is time-consuming and error-prone.

## Goals

1. Support bulk import from Excel/CSV files
2. Provide column mapping interface
3. Validate data before import
4. Show detailed error reports
5. Allow partial imports (skip bad rows)
6. Track import history

## User Stories

### US-001: Upload File
**As a** property manager
**I want to** upload a spreadsheet file
**So that** I can bulk import data

**Acceptance Criteria:**
- Support CSV, XLS, XLSX formats
- File size limit (configurable)
- Show upload progress
- Preview first rows

### US-002: Map Columns
**As a** property manager
**I want to** map spreadsheet columns to fields
**So that** data imports correctly

**Acceptance Criteria:**
- Auto-detect common column names
- Manual column mapping
- Mark required fields
- Skip unmapped columns
- Save mapping templates

### US-003: Validate Data
**As a** property manager
**I want to** see validation errors before import
**So that** I can fix issues

**Acceptance Criteria:**
- Validate required fields
- Validate data types/formats
- Check referential integrity
- Highlight error rows
- Show error counts by type

### US-004: Review and Import
**As a** property manager
**I want to** review and confirm import
**So that** I control what gets imported

**Acceptance Criteria:**
- Show valid vs error counts
- Option to skip errors
- Option to download error report
- Confirm import action
- Show import progress

### US-005: Import History
**As a** property manager
**I want to** see import history
**So that** I can track what was imported

**Acceptance Criteria:**
- List past imports
- Show status and counts
- View imported records
- Rollback option (soft delete)

## Technical Requirements

### Database Schema

```sql
-- import_batches table
CREATE TABLE import_batches (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),

    import_type ENUM('communities', 'buildings', 'units', 'contacts', 'owners', 'tenants', 'transactions'),
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    file_size INT,

    -- Mapping
    column_mapping JSON, -- {file_column: db_field, ...}
    mapping_template_id BIGINT REFERENCES import_mapping_templates(id),

    -- Stats
    total_rows INT,
    valid_rows INT DEFAULT 0,
    imported_rows INT DEFAULT 0,
    error_rows INT DEFAULT 0,
    skipped_rows INT DEFAULT 0,

    -- Status
    status ENUM('uploaded', 'mapping', 'validating', 'validated', 'importing', 'completed', 'failed', 'rolled_back') DEFAULT 'uploaded',

    -- Errors
    validation_errors JSON, -- [{row, field, error}, ...]
    import_errors JSON,

    imported_by BIGINT REFERENCES users(id),
    started_at TIMESTAMP,
    completed_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- import_mapping_templates table
CREATE TABLE import_mapping_templates (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    name VARCHAR(100),
    import_type VARCHAR(50),
    mapping JSON,
    is_default BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- import_batch_records table (track individual imported records)
CREATE TABLE import_batch_records (
    id BIGINT PRIMARY KEY,
    batch_id BIGINT REFERENCES import_batches(id),
    row_number INT,
    record_type VARCHAR(50),
    record_id BIGINT,
    status ENUM('imported', 'skipped', 'error'),
    error_message TEXT,
    created_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/import/upload | Upload file |
| GET | /api/import/{batch}/preview | Preview data |
| POST | /api/import/{batch}/mapping | Save column mapping |
| POST | /api/import/{batch}/validate | Validate data |
| GET | /api/import/{batch}/errors | Get validation errors |
| POST | /api/import/{batch}/import | Start import |
| GET | /api/import/{batch}/status | Get import status |
| POST | /api/import/{batch}/rollback | Rollback import |
| GET | /api/import/batches | List import batches |
| GET | /api/import/templates | List mapping templates |
| POST | /api/import/templates | Save mapping template |

### Import Types Configuration

```php
return [
    'communities' => [
        'model' => \App\Models\Community::class,
        'fields' => [
            'name' => ['type' => 'string', 'required' => true],
            'name_ar' => ['type' => 'string'],
            'code' => ['type' => 'string'],
            'address' => ['type' => 'string'],
            'city' => ['type' => 'string'],
            // ...
        ],
    ],
    'buildings' => [
        'model' => \App\Models\Building::class,
        'fields' => [
            'community_code' => ['type' => 'reference', 'ref' => 'communities.code', 'required' => true],
            'name' => ['type' => 'string', 'required' => true],
            'floors' => ['type' => 'integer'],
            // ...
        ],
    ],
    'units' => [
        'model' => \App\Models\Unit::class,
        'fields' => [
            'building_code' => ['type' => 'reference', 'ref' => 'buildings.code', 'required' => true],
            'number' => ['type' => 'string', 'required' => true],
            'type' => ['type' => 'enum', 'values' => ['studio', '1br', '2br', '3br', '4br', 'penthouse', 'villa']],
            // ...
        ],
    ],
];
```

### UI Components

1. **Upload Step** (`/import/{type}`)
   - Drag and drop zone
   - File type selector
   - Download template button

2. **Mapping Step**
   - Source columns list
   - Target fields list
   - Mapping lines
   - Auto-detect button
   - Template selector

3. **Validation Step**
   - Valid/Error counts
   - Error list with row numbers
   - Fix inline option
   - Download errors

4. **Import Step**
   - Progress bar
   - Records imported counter
   - Cancel button
   - Completion summary

5. **History** (`/import/history`)
   - Batch list
   - Filter by type/status
   - View details
   - Rollback action

## Captured Page Analysis

- `upload-community` - Community bulk upload
- `upload-building` - Building bulk upload
- `upload-unit` - Unit bulk upload
- `upload-contact` - Contact bulk upload
- `import-*` - Import related pages

## Validation Rules

| Import Type | Field | Rules |
|-------------|-------|-------|
| communities | name | Required, unique per tenant |
| buildings | community_code | Required, must exist |
| units | building_code | Required, must exist |
| units | number | Required, unique per building |
| contacts | email | Valid email, unique |
| contacts | phone | Valid phone format |

## Testing Requirements

1. **Unit Tests** - File parsing, validation, mapping
2. **Feature Tests** - Full import workflow
3. **E2E Tests** - Upload, map, validate, import
4. **Performance Tests** - Large file imports (10k+ rows)

## References

- Captured Pages: `docs/pages/upload-*`, `docs/pages/import-*`
