# Council Workflow

The full lifecycle of a feature, end-to-end, with the artifacts each step produces and the labels that change.

## States

`state:` labels track issue progression:

| Label | Meaning | Set by |
|---|---|---|
| `state:draft` | PRD or story is being authored | PM |
| `state:ready-for-ux` | Story is ready for the Designer | PM (after splitting PRD) |
| `state:ready-for-design` | Story is ready for the Tech Lead | Designer |
| `state:ready-for-impl` | Story is ready for the Engineer | Tech Lead |
| `state:in-progress` | Engineer is working on it | Engineer |
| `state:in-review` | PR is open, QA + Reviewer act here | Engineer |
| `state:blocked` | Something is preventing progress | Anyone (with comment) |
| `state:done` | PR merged, issue auto-closed | (auto via `Closes #N`) |

## The chain

### 1. Discovery and PRD — PM
**Trigger:** `/pm-prd <topic>` or `/feature <topic>`
**Reads:** `.claude/agent-memory/pm/MEMORY.md`, recent PRDs (`gh issue list --label type:prd`)
**Runs:** `prd-development` skill (full workflow)
**Produces:** PRD issue via `prd.yml` template, labeled `type:prd state:draft area:<inferred> agent:pm`
**Output:** PRD issue URL

🛑 **Checkpoint:** human reads the PRD, requests tweaks, or approves.

### 2. Story split — PM
**Trigger:** `/pm-stories <prd#>`
**Runs:** `user-story` + `user-story-splitting` skills
**Produces:** N story issues via `user-story.yml`, each linked to PRD with `Part of #<prd>`, labeled `type:story state:ready-for-ux area:<same> agent:pm`
**Output:** list of story URLs

🛑 **Checkpoint:** human picks which stories to advance.

### 3. UX flow — Designer (if UI scope)
**Trigger:** `/design-flow <story#>`
**Reads:** the story, sibling pages in `resources/js/pages/<area>/`
**Produces:** UX comment on the story (sections: User flow, Key screens, Microcopy, States, Accessibility, RTL notes)
**Relabels:** `state:ready-for-ux` → `state:ready-for-design`, `agent:designer`
**Output:** comment URL

🛑 **Checkpoint:** human approves UX flow.

### 4. Technical design — Tech Lead
**Trigger:** `/tl-design <story#>`
**Reads:** the story, UX comment (if any), affected models/controllers/pages
**Produces:** tech-design comment on the story (sections: Approach, Files to touch, Data model, API/contract, Risks, Test plan)
**Relabels:** `state:ready-for-design` → `state:ready-for-impl`, `agent:tech-lead`
**Output:** comment URL

🛑 **Checkpoint:** human approves design.

### 5. Implementation — Engineer
**Trigger:** `/eng-implement <story#>`
**Reads:** story, UX, design
**Produces:**
- New branch `feat/<short>` off `1.x`.
- Code changes per design's "Files to touch".
- Wayfinder regen if needed.
- At least one happy-path PHPUnit feature test.
- Pint formatted, tests passing.
- PR with `Closes #<story>` body, labeled `state:in-review agent:engineer`.

**Relabels story:** `state:ready-for-impl` → `state:in-review`, `agent:engineer`
**Output:** PR URL

🛑 **Checkpoint:** human reviews PR readiness.

### 6. QA tests — QA
**Trigger:** `/qa-test <pr#>`
**Reads:** PR diff, linked issue, existing tests
**Produces:**
- Additional PHPUnit tests covering every uncovered AC + at least one failure path + at least one edge case, committed to the PR branch.
- AC-mapped report comment on the PR (`AC | Status | Test` table).

**Output:** comment URL, pass/fail tally

If QA is red → ping Engineer to fix; loop.

### 7. Code review — Reviewer
**Trigger:** `/review <pr#>`
**Reads:** PR diff, linked issue, design comment, QA report
**Produces:** structured review via `gh pr review --approve` or `--request-changes`, with file:line citations
**Output:** review URL

If `request-changes` → Engineer addresses; loop.
If `approve` → 🛑 **human merges the PR** (Reviewer never merges).

### 8. Project sync — Delivery PM
**Trigger:** any time after the above (or `/dpm-status`, `/dpm-plan`)
**Reads:** Product Council project, all open issues
**Produces:**
- Project items added/updated for every touched issue.
- Status, Type, Area, Priority, Agent fields synced from labels.
- Sprint health report (on `/dpm-status`).
- Sprint plan proposal (on `/dpm-plan <milestone>`).

**Output:** status report or proposal table.

## Resuming across sessions

The council is fully resumable. To pick up where you left off:

```
/handoff <issue-number>
```

This reads the issue's `state:*` label and dispatches to the correct next agent. You can also browse the project board (Status column = exact state) to find whatever is `Ready` or `In Review`.

## When to use `/feature` vs atomic commands

- `/feature <topic>` — best for greenfield work where you want the full lifecycle.
- `/handoff <issue#>` — best for picking up mid-stream work.
- Atomic commands (`/pm-prd`, `/tl-design`, etc.) — best for surgical operations or when you only want one specific deliverable.

## When the chain breaks

- **PM finds the topic ill-formed** → adds `state:blocked` and comments with what's needed.
- **Designer says "no UX scope"** → relabels back to `state:ready-for-design,agent:pm` and pings Tech Lead directly.
- **Tech Lead says "story too big"** → relabels back to `state:ready-for-ux,agent:pm` with a recommended split.
- **Engineer disagrees with design** → comments on the issue, waits for Tech Lead response (does not silently diverge).
- **QA is red** → comments diagnosis, pings Engineer; PR remains `state:in-review` until tests are green.
- **Reviewer requests changes** → Engineer fixes; loop until approved.
