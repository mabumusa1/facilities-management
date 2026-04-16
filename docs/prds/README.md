# Product Requirements Documents (PRDs)

This directory contains detailed PRDs for the Facilities Management System, organized by priority and milestone.

## Overview

| Document | Description |
|----------|-------------|
| [00-GAP-ANALYSIS.md](./00-GAP-ANALYSIS.md) | Gap analysis comparing captured pages vs existing codebase |

## PRD Index by Milestone

### M0 - Foundation
| PRD # | Title | Priority | Est. Effort | Status |
|-------|-------|----------|-------------|--------|
| [PRD-021](./PRD-021-company-profile-settings.md) | Company Profile Settings | High | 1 week | Written |

### M2 - Leasing
| PRD # | Title | Priority | Est. Effort | Status |
|-------|-------|----------|-------------|--------|
| [PRD-002](./PRD-002-contract-types-settings.md) | Contract Types Settings | High (BLOCKER) | 1 week | Written |
| [PRD-001](./PRD-001-leasing-quotes-workflow.md) | Leasing Quotes Workflow | High | 2-3 weeks | Written |
| [PRD-016](./PRD-016-move-out-workflow.md) | Move-out Workflow | High | 2 weeks | Written |

### M3 - Service Operations
| PRD # | Title | Priority | Est. Effort | Status |
|-------|-------|----------|-------------|--------|
| [PRD-003](./PRD-003-service-request-settings.md) | Service Request Settings | High | 2 weeks | Written |
| [PRD-004](./PRD-004-home-services-configuration.md) | Home Services Configuration | High | 1-2 weeks | Written |
| [PRD-005](./PRD-005-neighbourhood-services.md) | Neighbourhood Services | Medium | 1-2 weeks | Written |

### M3 - Financial
| PRD # | Title | Priority | Est. Effort | Status |
|-------|-------|----------|-------------|--------|
| [PRD-006](./PRD-006-transaction-recording.md) | Transaction Recording | High | 2-3 weeks | Written |
| [PRD-007](./PRD-007-transaction-schedules.md) | Transaction Schedules | Medium | 1-2 weeks | Written |
| [PRD-022](./PRD-022-invoice-settings.md) | Invoice Settings | High | 1 week | Written |
| [PRD-023](./PRD-023-bank-details-settings.md) | Bank Details Settings | High | 3-5 days | Written |

### M4 - Operations
| PRD # | Title | Priority | Est. Effort | Status |
|-------|-------|----------|-------------|--------|
| [PRD-017](./PRD-017-bulk-upload-features.md) | Bulk Upload Features | Medium | 2 weeks | Written |

### M5 - Visitor & Facilities
| PRD # | Title | Priority | Est. Effort | Status |
|-------|-------|----------|-------------|--------|
| [PRD-008](./PRD-008-visitor-access-module.md) | Visitor Access Module | High | 2-3 weeks | Written |
| [PRD-009](./PRD-009-facilities-booking.md) | Facilities Booking | High | 2-3 weeks | Written |
| [PRD-025](./PRD-025-booking-contracts.md) | Booking Contracts | Medium | 1-2 weeks | Written |
| [PRD-020](./PRD-020-dashboard-enhancements.md) | Dashboard Enhancements | Medium | 2 weeks | Written |

### M6 - Communication
| PRD # | Title | Priority | Est. Effort | Status |
|-------|-------|----------|-------------|--------|
| [PRD-010](./PRD-010-offers-management.md) | Offers Management | Medium | 1-2 weeks | Written |
| [PRD-011](./PRD-011-suggestions-module.md) | Suggestions Module | Low | 1 week | Written |
| [PRD-012](./PRD-012-directory-management.md) | Directory Management | Medium | 1-2 weeks | Written |
| [PRD-024](./PRD-024-complaints-module.md) | Complaints Module | Medium | 1-2 weeks | Written |

### M7 - Marketplace
| PRD # | Title | Priority | Est. Effort | Status |
|-------|-------|----------|-------------|--------|
| [PRD-013](./PRD-013-marketplace-core.md) | Marketplace Core | High | 4-6 weeks | Written |
| [PRD-014](./PRD-014-marketplace-customers.md) | Marketplace Customers | High | 2 weeks | Written |
| [PRD-015](./PRD-015-marketplace-visits.md) | Marketplace Visits | Medium | 1-2 weeks | Written |

### M9 - Reporting
| PRD # | Title | Priority | Est. Effort | Status |
|-------|-------|----------|-------------|--------|
| [PRD-018](./PRD-018-system-reports.md) | System Reports | Medium | 3-4 weeks | Written |
| [PRD-019](./PRD-019-power-bi-integration.md) | Power BI Integration | Low | 2 weeks | Written |

## Implementation Order

Based on dependencies and business priority:

```
Phase 1 (Weeks 1-2): Foundation & Blockers
├── PRD-021: Company Profile Settings
├── PRD-002: Contract Types Settings (BLOCKER)
└── PRD-003: Service Request Settings

Phase 2 (Weeks 3-5): Core Leasing
├── PRD-001: Leasing Quotes Workflow
└── PRD-016: Move-out Workflow

Phase 3 (Weeks 6-8): Financial
├── PRD-006: Transaction Recording
├── PRD-007: Transaction Schedules
├── PRD-022: Invoice Settings
└── PRD-023: Bank Details Settings

Phase 4 (Weeks 9-10): Service Operations
├── PRD-004: Home Services Configuration
└── PRD-005: Neighbourhood Services

Phase 5 (Weeks 11-14): Visitor & Facilities
├── PRD-008: Visitor Access Module
├── PRD-009: Facilities Booking
├── PRD-025: Booking Contracts
└── PRD-020: Dashboard Enhancements

Phase 6 (Weeks 15-20): Marketplace
├── PRD-013: Marketplace Core
├── PRD-014: Marketplace Customers
└── PRD-015: Marketplace Visits

Phase 7 (Weeks 21-23): Communication
├── PRD-010: Offers Management
├── PRD-011: Suggestions Module
├── PRD-012: Directory Management
└── PRD-024: Complaints Module

Phase 8 (Weeks 24-26): Operations & Reporting
├── PRD-017: Bulk Upload Features
├── PRD-018: System Reports
└── PRD-019: Power BI Integration
```

## PRD Template

Each PRD follows this structure:

1. **Overview** - Priority, milestone, effort, dependencies
2. **Problem Statement** - What problem we're solving
3. **Goals** - What we want to achieve
4. **User Stories** - Detailed acceptance criteria
5. **Technical Requirements** - Database schema, API endpoints
6. **UI Components** - Page layouts and components
7. **Captured Page Analysis** - Reference to goatar.com captures
8. **Validation Rules** - Data validation
9. **Testing Requirements** - Unit, feature, E2E tests
10. **References** - Links to docs and captures

## GitHub Labels

| Label | Description |
|-------|-------------|
| `prd` | Product Requirements Document |
| `priority:high` | High priority |
| `priority:medium` | Medium priority |
| `priority:low` | Low priority |
| `blocker` | Blocks other work |
| `milestone:M0` - `milestone:M9` | Milestone tags |

## Source Data

- Captured pages: `docs/pages/` (259 directories)
- API specifications: `docs/api/`
- Scanner tests: `docs/scanner/tests/agents/`
