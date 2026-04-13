/**
 * Workflow PRD Template
 *
 * For business workflow and process definitions
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
  const workflowName = prd.title.replace('PRD: ', '');

  return `## Overview

${prd.overview || `Implement the ${workflowName} workflow with proper state management and user interactions.`}

## User Stories

- As a **user**, I want to initiate ${workflowName.toLowerCase()}, so that I can accomplish my task efficiently.
- As a **manager**, I want to track ${workflowName.toLowerCase()} progress, so that I can monitor operations.
- As a **system**, I want to enforce ${workflowName.toLowerCase()} rules, so that data integrity is maintained.

## Workflow States

\`\`\`
[Initial] --> [In Progress] --> [Review] --> [Completed]
     |              |              |
     v              v              v
  [Cancelled]   [On Hold]    [Rejected]
\`\`\`

### State Definitions

| State | Description | Allowed Transitions |
|-------|-------------|---------------------|
| Initial | Starting state | In Progress, Cancelled |
| In Progress | Being worked on | Review, On Hold, Cancelled |
| Review | Awaiting approval | Completed, Rejected |
| Completed | Successfully finished | - |
| Cancelled | User cancelled | - |

## Laravel Implementation

### State Machine

Using \`spatie/laravel-model-states\` or custom implementation:

\`\`\`php
<?php

namespace App\\States;

use Spatie\\ModelStates\\State;
use Spatie\\ModelStates\\StateConfig;

abstract class WorkflowState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Initial::class)
            ->allowTransition(Initial::class, InProgress::class)
            ->allowTransition(InProgress::class, Review::class)
            ->allowTransition(Review::class, Completed::class)
            ->allowTransition([Initial::class, InProgress::class], Cancelled::class);
    }
}
\`\`\`

### Actions / Service Class

\`\`\`php
<?php

namespace App\\Actions;

class ${workflowName.replace(/\s+/g, '').replace(/&/g, 'And')}Action
{
    public function execute(Model $model, array $data): Model
    {
        // Validate state transition
        // Perform business logic
        // Update model state
        // Trigger events/notifications

        return $model;
    }
}
\`\`\`

### Events

\`\`\`php
// Events to trigger
class WorkflowStarted {}
class WorkflowCompleted {}
class WorkflowCancelled {}
\`\`\`

### Notifications

\`\`\`php
class WorkflowStatusNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }
}
\`\`\`

## API Endpoints

| Method | URI | Action | Description |
|--------|-----|--------|-------------|
| POST | \`/api/workflow/start\` | start | Initiate workflow |
| POST | \`/api/workflow/{id}/transition\` | transition | Change state |
| GET | \`/api/workflow/{id}/history\` | history | Get state history |
| POST | \`/api/workflow/{id}/cancel\` | cancel | Cancel workflow |

## Livewire/Inertia Components

### Workflow Wizard

\`\`\`php
class WorkflowWizard extends Component
{
    public int $step = 1;
    public array $data = [];

    public function nextStep(): void
    {
        $this->validateStep();
        $this->step++;
    }

    public function submit(): void
    {
        // Execute workflow action
    }
}
\`\`\`

### Progress Tracker

Display current state and available actions.

## Permissions

| Action | Permission | Roles |
|--------|------------|-------|
| Start workflow | start-workflow | Manager, Admin |
| Approve/Reject | approve-workflow | Supervisor, Admin |
| Cancel | cancel-workflow | Manager, Admin |
| View history | view-workflow | All authenticated |

## Business Rules

### Validation Rules

- Define per-step validation requirements
- Conditional field requirements based on state

### Timeout Rules

- Define SLA timings if applicable
- Auto-escalation rules

### Notification Triggers

| Event | Recipients | Channel |
|-------|------------|---------|
| Started | Assignee | Email, In-app |
| Needs Review | Approver | Email, In-app |
| Completed | Initiator | Email |

## Dependencies

${dependencyLinks ? `- Depends on: ${dependencyLinks}` : '- None'}

## Acceptance Criteria

- [ ] State machine implemented correctly
- [ ] All state transitions validated
- [ ] Actions/services created and tested
- [ ] Events dispatched on state changes
- [ ] Notifications sent to appropriate users
- [ ] API endpoints functional
- [ ] UI wizard/form working
- [ ] History/audit trail captured
- [ ] Feature tests covering all transitions

## Error Handling

- Invalid state transitions should return clear error messages
- Failed operations should roll back cleanly
- Users should see actionable error feedback

## References

- Source: \`src/api/docs/${prd.source}\`
- State Machine Package: https://spatie.be/docs/laravel-model-states
`;
}
