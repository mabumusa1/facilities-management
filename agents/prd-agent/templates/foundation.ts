/**
 * Foundation PRD Template
 *
 * For foundational system requirements (RBAC, Auth, Reference Data)
 */

import { PRDConfig } from '../config';
import { ArtifactData } from '../artifact-reader';

export interface TemplateData {
  prd: PRDConfig;
  sourceData: ArtifactData;
  dependencyLinks: string;
  issueMap: Map<number, number>;
}

export function render(data: TemplateData): string {
  const { prd, dependencyLinks } = data;

  return `## Overview

${prd.overview || 'This PRD defines foundational requirements for the facilities management system.'}

## User Stories

- As a **system administrator**, I want to configure ${prd.title.replace('PRD: ', '').toLowerCase()}, so that the platform has proper access control.
- As a **developer**, I want clear specifications for ${prd.title.replace('PRD: ', '').toLowerCase()}, so that I can implement it correctly in Laravel.

## Laravel Implementation

### Configuration

This is a foundational component that should be implemented early in the project setup.

#### Packages Required
- \`spatie/laravel-permission\` - For role and permission management
- \`laravel/sanctum\` - For API authentication

### Database Migrations

\`\`\`php
// Example migration structure
Schema::create('table_name', function (Blueprint $table) {
    $table->id();
    // Add fields based on source documentation
    $table->timestamps();
});
\`\`\`

### Models

- Location: \`app/Models/\`
- Implement appropriate relationships and scopes

### Seeders

- Create seeders for initial data population
- Location: \`database/seeders/\`

## Permissions Required

| Permission | Description |
|------------|-------------|
| manage-system | Full system configuration access |
| view-settings | View system settings |

## Business Rules

Refer to source documentation for detailed business rules.

## Dependencies

${dependencyLinks ? `- Depends on: ${dependencyLinks}` : '- None (foundational component)'}

## Acceptance Criteria

- [ ] Database migrations created and tested
- [ ] Models with relationships implemented
- [ ] Seeders created for initial data
- [ ] Unit tests written and passing
- [ ] Integration with authentication system verified

## Technical Notes

- This is a foundational component that other modules depend on
- Ensure backward compatibility when making changes
- Follow Laravel conventions for naming and structure

## References

- Source: \`src/api/docs/${prd.source}\`
- Laravel Documentation: https://laravel.com/docs
- Spatie Permission: https://spatie.be/docs/laravel-permission
`;
}
