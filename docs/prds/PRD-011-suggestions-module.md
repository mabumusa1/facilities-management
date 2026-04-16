# PRD-011: Suggestions Module

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Low |
| **Milestone** | M6 - Communication |
| **Estimated Effort** | 1 week |
| **Dependencies** | None |
| **Related Pages** | communication-suggestions, dashboard-suggestions-* |

## Problem Statement

Tenants want to provide suggestions for community improvements. Property managers need a structured way to collect, review, and act on tenant feedback.

## Goals

1. Enable tenants to submit suggestions
2. Categorize and prioritize suggestions
3. Track suggestion status and response
4. Engage community through voting
5. Communicate actions taken

## User Stories

### US-001: Submit Suggestion
**As a** tenant
**I want to** submit improvement suggestions
**So that** my community can improve

**Acceptance Criteria:**
- Select category
- Enter suggestion title and description
- Add supporting photos
- Submit anonymously option
- Receive confirmation

### US-002: Review Suggestions
**As a** property manager
**I want to** review submitted suggestions
**So that** I can evaluate feasibility

**Acceptance Criteria:**
- View all suggestions
- Filter by category, status, date
- Read full details
- See submitter (if not anonymous)
- Mark as reviewed

### US-003: Respond to Suggestions
**As a** property manager
**I want to** respond to suggestions
**So that** tenants know their feedback is valued

**Acceptance Criteria:**
- Add response/comment
- Update status (Under Review, Approved, Implemented, Declined)
- Provide reason for decision
- Notify submitter

### US-004: Vote on Suggestions
**As a** tenant
**I want to** vote on other suggestions
**So that** popular ideas get attention

**Acceptance Criteria:**
- View community suggestions
- Upvote/downvote
- See vote counts
- Sort by popularity

## Technical Requirements

### Database Schema

```sql
-- suggestions table
CREATE TABLE suggestions (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    contact_id BIGINT REFERENCES contacts(id),
    community_id BIGINT REFERENCES communities(id),

    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('amenities', 'maintenance', 'security', 'events', 'policies', 'other'),
    photos JSON,

    is_anonymous BOOLEAN DEFAULT false,
    status ENUM('submitted', 'under_review', 'approved', 'implemented', 'declined') DEFAULT 'submitted',

    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,

    response TEXT,
    responded_by BIGINT REFERENCES users(id),
    responded_at TIMESTAMP,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- suggestion_votes table
CREATE TABLE suggestion_votes (
    id BIGINT PRIMARY KEY,
    suggestion_id BIGINT REFERENCES suggestions(id),
    contact_id BIGINT REFERENCES contacts(id),
    vote_type ENUM('up', 'down'),
    created_at TIMESTAMP,
    UNIQUE(suggestion_id, contact_id)
);

-- suggestion_comments table
CREATE TABLE suggestion_comments (
    id BIGINT PRIMARY KEY,
    suggestion_id BIGINT REFERENCES suggestions(id),
    contact_id BIGINT REFERENCES contacts(id),
    user_id BIGINT REFERENCES users(id),
    comment TEXT,
    is_official BOOLEAN DEFAULT false,
    created_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/suggestions | List suggestions |
| POST | /api/suggestions | Submit suggestion |
| GET | /api/suggestions/{id} | Get suggestion |
| PUT | /api/suggestions/{id} | Update suggestion |
| POST | /api/suggestions/{id}/respond | Add response |
| POST | /api/suggestions/{id}/vote | Vote |
| GET | /api/suggestions/{id}/comments | Get comments |
| POST | /api/suggestions/{id}/comments | Add comment |

### UI Components

1. **Suggestions List** (`/suggestions`)
   - Suggestion cards
   - Category filter
   - Sort by votes/date
   - Submit button

2. **Submit Form**
   - Category selector
   - Title and description
   - Photo upload
   - Anonymous toggle

3. **Suggestion Detail**
   - Full content
   - Vote buttons
   - Comments section
   - Status badge
   - Official response

4. **Manager Dashboard** (`/dashboard/suggestions`)
   - Pending review list
   - Quick actions
   - Statistics

## Captured Page Analysis

- `communication-suggestions` - Suggestions list
- `dashboard-suggestions` - Manager view
- `dashboard-suggestions-details` - Detail view

## Testing Requirements

1. **Unit Tests** - Voting logic, status transitions
2. **Feature Tests** - CRUD, voting, responding
3. **E2E Tests** - Submit suggestion, manager response

## References

- Captured Pages: `docs/pages/communication-suggestions`, `docs/pages/dashboard-suggestions-*`
