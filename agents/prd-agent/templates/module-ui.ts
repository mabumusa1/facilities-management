/**
 * Module UI PRD Template
 *
 * For module UI/frontend specifications
 */

import type { ArtifactData } from '../artifact-reader';
import type { PRDConfig } from '../config';

export interface TemplateData {
  prd: PRDConfig;
  sourceData: ArtifactData;
  dependencyLinks: string;
  issueMap: Map<number, number>;
}

export function render(data: TemplateData): string {
  const { prd, dependencyLinks } = data;
  const moduleName = prd.title.replace('PRD: ', '').replace(' Module UI', '').replace(' UI', '');

  return `## Overview

${prd.overview || `Implement the ${moduleName} module user interface with all required views, forms, and interactions.`}

## User Stories

- As a **user**, I want to navigate the ${moduleName} module easily, so that I can accomplish my tasks efficiently.
- As a **manager**, I want to view ${moduleName.toLowerCase()} data in organized lists, so that I can make informed decisions.
- As an **admin**, I want to manage ${moduleName.toLowerCase()} records through intuitive forms, so that data entry is quick and accurate.

## Navigation Structure

\`\`\`
/${moduleName.toLowerCase().replace(/\s+/g, '-')}/
├── index          # List view with search/filter
├── create         # Create new record form
├── {id}           # View single record details
├── {id}/edit      # Edit record form
└── settings       # Module settings (if applicable)
\`\`\`

## Laravel Routes

\`\`\`php
Route::prefix('${moduleName.toLowerCase().replace(/\s+/g, '-')}')->group(function () {
    Route::get('/', [${moduleName.replace(/\s+/g, '')}Controller::class, 'index'])->name('${moduleName.toLowerCase().replace(/\s+/g, '-')}.index');
    Route::get('/create', [${moduleName.replace(/\s+/g, '')}Controller::class, 'create'])->name('${moduleName.toLowerCase().replace(/\s+/g, '-')}.create');
    Route::post('/', [${moduleName.replace(/\s+/g, '')}Controller::class, 'store'])->name('${moduleName.toLowerCase().replace(/\s+/g, '-')}.store');
    Route::get('/{id}', [${moduleName.replace(/\s+/g, '')}Controller::class, 'show'])->name('${moduleName.toLowerCase().replace(/\s+/g, '-')}.show');
    Route::get('/{id}/edit', [${moduleName.replace(/\s+/g, '')}Controller::class, 'edit'])->name('${moduleName.toLowerCase().replace(/\s+/g, '-')}.edit');
    Route::put('/{id}', [${moduleName.replace(/\s+/g, '')}Controller::class, 'update'])->name('${moduleName.toLowerCase().replace(/\s+/g, '-')}.update');
    Route::delete('/{id}', [${moduleName.replace(/\s+/g, '')}Controller::class, 'destroy'])->name('${moduleName.toLowerCase().replace(/\s+/g, '-')}.destroy');
});
\`\`\`

## Page Components

### List View (Index)

**Features:**
- Searchable data table
- Column sorting
- Pagination
- Bulk actions
- Quick filters
- Export options

\`\`\`php
// Livewire Component
class ${moduleName.replace(/\s+/g, '')}List extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public array $filters = [];

    public function render()
    {
        $items = ${moduleName.replace(/\s+/g, '')}::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.${moduleName.toLowerCase().replace(/\s+/g, '-')}.list', compact('items'));
    }
}
\`\`\`

### Detail View (Show)

**Features:**
- Summary cards/stats
- Related data tabs
- Action buttons
- Activity timeline
- Quick edit capabilities

### Create/Edit Form

**Features:**
- Multi-step wizard (if complex)
- Real-time validation
- Auto-save drafts
- File uploads
- Confirmation on submit

\`\`\`php
// Livewire Form Component
class ${moduleName.replace(/\s+/g, '')}Form extends Component
{
    public ${moduleName.replace(/\s+/g, '')} $model;

    protected $rules = [
        // Validation rules
    ];

    public function save()
    {
        $this->validate();
        $this->model->save();

        session()->flash('success', 'Saved successfully!');
        return redirect()->route('${moduleName.toLowerCase().replace(/\s+/g, '-')}.show', $this->model);
    }
}
\`\`\`

## UI Components

### Shared Components

| Component | Purpose |
|-----------|---------|
| DataTable | Reusable table with sorting/filtering |
| SearchInput | Search with debounce |
| StatusBadge | Display status with color coding |
| ActionDropdown | Context menu for row actions |
| ConfirmModal | Confirmation dialog for destructive actions |
| EmptyState | Display when no data |

### Module-Specific Components

| Component | Description |
|-----------|-------------|
| ${moduleName}Card | Summary card for dashboard |
| ${moduleName}Stats | Statistics display |
| ${moduleName}Timeline | Activity history |

## Permissions in UI

\`\`\`blade
@can('create', App\\Models\\${moduleName.replace(/\s+/g, '')}::class)
    <x-button href="{{ route('${moduleName.toLowerCase().replace(/\s+/g, '-')}.create') }}">
        Create New
    </x-button>
@endcan

@can('update', $model)
    <x-button href="{{ route('${moduleName.toLowerCase().replace(/\s+/g, '-')}.edit', $model) }}">
        Edit
    </x-button>
@endcan

@can('delete', $model)
    <x-button wire:click="delete({{ $model->id }})" color="danger">
        Delete
    </x-button>
@endcan
\`\`\`

## Responsive Design

- Mobile-first approach
- Collapsible sidebar on mobile
- Touch-friendly actions
- Swipe gestures for common actions

## Accessibility

- Keyboard navigation support
- ARIA labels on interactive elements
- Focus management on modals
- Screen reader announcements for actions

## Loading States

- Skeleton loaders for initial load
- Inline spinners for actions
- Optimistic UI updates where appropriate

## Error Handling

- Form validation errors inline
- Toast notifications for action feedback
- Error boundaries for component failures
- Retry mechanisms for failed API calls

## Dependencies

${dependencyLinks ? `- Depends on: ${dependencyLinks}` : '- None'}

## Acceptance Criteria

- [ ] All routes implemented and working
- [ ] List view with search, sort, filter, pagination
- [ ] Detail view showing all relevant data
- [ ] Create/Edit forms with validation
- [ ] Delete with confirmation
- [ ] Permission checks in all views
- [ ] Mobile responsive
- [ ] Loading states implemented
- [ ] Error handling for all scenarios
- [ ] Browser tests written and passing

## Design System

Follow the established design system:
- Colors: Use theme variables
- Typography: System font stack
- Spacing: Tailwind spacing scale
- Components: Laravel Blade/Livewire components

## References

- Source: \`src/api/docs/${prd.source}\`
- Livewire Docs: https://livewire.laravel.com
- Alpine.js: https://alpinejs.dev
`;
}
