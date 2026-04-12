/**
 * Data Model PRD Template
 *
 * For entity/data model definitions
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
  const { prd, sourceData, dependencyLinks } = data;
  const entityName = prd.title.replace('PRD: ', '').replace(' Entity', '').replace('Contacts - ', '');

  return `## Overview

${prd.overview || `Define the ${entityName} entity with its attributes, relationships, and business rules.`}

## User Stories

- As a **property manager**, I want to manage ${entityName.toLowerCase()} records, so that I can track and organize them effectively.
- As a **system user**, I want to view ${entityName.toLowerCase()} details, so that I can access relevant information.
- As an **administrator**, I want to configure ${entityName.toLowerCase()} settings, so that the system meets our requirements.

## Laravel Implementation

### Model

\`\`\`php
<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\SoftDeletes;
use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;

class ${entityName.replace(/\s+/g, '')} extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Define fillable fields
    ];

    protected $casts = [
        // Define attribute casts
    ];

    // Define relationships
}
\`\`\`

### Migration

\`\`\`php
Schema::create('${entityName.toLowerCase().replace(/\s+/g, '_')}s', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
    // Add entity-specific fields based on source documentation
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->timestamps();
    $table->softDeletes();
});
\`\`\`

### Eloquent Relationships

Define based on entity relationships in source documentation.

### Form Request Validation

\`\`\`php
// Store${entityName.replace(/\s+/g, '')}Request
public function rules(): array
{
    return [
        // Define validation rules
    ];
}

// Update${entityName.replace(/\s+/g, '')}Request
public function rules(): array
{
    return [
        // Define validation rules
    ];
}
\`\`\`

### Policy

\`\`\`php
class ${entityName.replace(/\s+/g, '')}Policy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-${entityName.toLowerCase().replace(/\s+/g, '-')}');
    }

    public function view(User $user, ${entityName.replace(/\s+/g, '')} $model): bool
    {
        return $user->can('view-${entityName.toLowerCase().replace(/\s+/g, '-')}');
    }

    public function create(User $user): bool
    {
        return $user->can('create-${entityName.toLowerCase().replace(/\s+/g, '-')}');
    }

    public function update(User $user, ${entityName.replace(/\s+/g, '')} $model): bool
    {
        return $user->can('update-${entityName.toLowerCase().replace(/\s+/g, '-')}');
    }

    public function delete(User $user, ${entityName.replace(/\s+/g, '')} $model): bool
    {
        return $user->can('delete-${entityName.toLowerCase().replace(/\s+/g, '-')}');
    }
}
\`\`\`

## API Endpoints

| Method | URI | Action | Description |
|--------|-----|--------|-------------|
| GET | \`/api/${entityName.toLowerCase().replace(/\s+/g, '-')}s\` | index | List all records |
| POST | \`/api/${entityName.toLowerCase().replace(/\s+/g, '-')}s\` | store | Create new record |
| GET | \`/api/${entityName.toLowerCase().replace(/\s+/g, '-')}s/{id}\` | show | Get single record |
| PUT | \`/api/${entityName.toLowerCase().replace(/\s+/g, '-')}s/{id}\` | update | Update record |
| DELETE | \`/api/${entityName.toLowerCase().replace(/\s+/g, '-')}s/{id}\` | destroy | Delete record |

## Filament Resource

\`\`\`php
class ${entityName.replace(/\s+/g, '')}Resource extends Resource
{
    protected static ?string $model = ${entityName.replace(/\s+/g, '')}::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Define form fields
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            // Define table columns
        ]);
    }
}
\`\`\`

## Permissions

| Permission | Roles |
|------------|-------|
| view-${entityName.toLowerCase().replace(/\s+/g, '-')} | Admin, Manager |
| create-${entityName.toLowerCase().replace(/\s+/g, '-')} | Admin, Manager |
| update-${entityName.toLowerCase().replace(/\s+/g, '-')} | Admin, Manager |
| delete-${entityName.toLowerCase().replace(/\s+/g, '-')} | Admin |

## Business Rules

Refer to source documentation for specific business rules and constraints.

## Dependencies

${dependencyLinks ? `- Depends on: ${dependencyLinks}` : '- None'}

## Acceptance Criteria

- [ ] Model created with proper fillable fields and casts
- [ ] Migration created and tested
- [ ] Relationships implemented correctly
- [ ] Form Request validation working
- [ ] Policy implemented and registered
- [ ] API endpoints functional
- [ ] Filament resource created (if admin panel)
- [ ] Factory and seeder created
- [ ] Feature tests written and passing

## Database Seeders

\`\`\`php
class ${entityName.replace(/\s+/g, '')}Seeder extends Seeder
{
    public function run(): void
    {
        ${entityName.replace(/\s+/g, '')}::factory()->count(10)->create();
    }
}
\`\`\`

## References

- Source: \`src/api/${prd.source}\`
`;
}
