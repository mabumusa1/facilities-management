# Agents Reference

Quick reference for each agent and the slash commands that invoke them.

## Slash commands

### Atomic invocations

| Command | Agent | Produces |
|---|---|---|
| `/pm-prd <topic>` | pm | PRD issue (`prd.yml`) |
| `/pm-stories <prd#>` | pm | N user-story issues (`user-story.yml`), each linked to the PRD |
| `/design-flow <story#>` | designer | UX-flow comment on the story |
| `/tl-design <story#>` | tech-lead | Technical design comment on the story |
| `/eng-implement <story#>` | engineer | PR closing the story, with happy-path tests |
| `/qa-test <pr#>` | qa | Additional tests + AC-mapped report comment on PR |
| `/review <pr#>` | reviewer | `gh pr review` (approve or request-changes); on approve, relabels to `state:ready-for-docs` |
| `/docs-feature <story#>` | docs | User guide (EN+AR) + CHANGELOG entry pushed to the story PR branch (chain mode) |
| `/docs-guide <topic-or-area>` | docs | Standalone user guide in a dedicated docs PR |
| `/docs-changelog <version>` | docs | Release cut — moves `[Unreleased]` into a versioned section |
| `/dpm-status` | delivery-pm | Project board status report (in-message) |
| `/dpm-plan <milestone>` | delivery-pm | Sprint plan proposal table (in-message) |

### Conductor invocations

| Command | Behavior |
|---|---|
| `/feature <topic>` | Runs the full chain end-to-end with mandatory checkpoints after every step |
| `/handoff <issue#>` | Reads the issue's `state:` label and dispatches to the correct next agent |

## Agent quick reference

### `pm` (Product Manager) 🔵
- **Charter:** discovery, PRDs, user stories, prioritization. Never writes code.
- **Tools:** Read, Glob, Grep, Bash (no Write/Edit on source — only memory)
- **Skills loaded:** `prd-development`, `problem-statement`, `discovery-process`, `jobs-to-be-done`, `proto-persona`, `prioritization-advisor`, `epic-hypothesis`, `problem-framing-canvas`, `opportunity-solution-tree`, `user-story`, `user-story-splitting`
- **Invokes:** `/pm-prd`, `/pm-stories`

### `tech-lead` (Tech Lead / Architect) 🟣
- **Charter:** translate stories into tech designs. Never writes code, never opens PRs.
- **Tools:** Read, Glob, Grep, Bash
- **Skills loaded:** `laravel-best-practices`, `epic-breakdown-advisor`
- **Invokes:** `/tl-design`

### `designer` (UX Designer) 🩷
- **Charter:** UX flows, wireframes (markdown/ASCII), microcopy, a11y, RTL/Arabic. Never writes code, never specifies database schemas.
- **Tools:** Read, Glob, Grep, Bash
- **Skills loaded:** `customer-journey-map`, `storyboard`, `user-story-mapping`, `tailwindcss-development`
- **Invokes:** `/design-flow`

### `delivery-pm` (Delivery Project Manager) 🩵
- **Charter:** project board, milestones, sprint planning, status, blockers. Never modifies issue bodies.
- **Tools:** Read, Glob, Grep, Bash
- **Skills loaded:** `roadmap-planning`, `prioritization-advisor`
- **Invokes:** `/dpm-status`, `/dpm-plan`, plus auto-sync on label changes

### `engineer` (Engineer) 🟢
- **Charter:** implement stories, open PRs, write happy-path tests. Never approves own PR, never merges.
- **Tools:** Read, Glob, Grep, Bash, Write, Edit
- **Skills loaded:** `laravel-best-practices`, `fortify-development`, `wayfinder-development`, `inertia-vue-development`, `tailwindcss-development`
- **Invokes:** `/eng-implement`

### `qa` (QA / Test Author) 🟡
- **Charter:** AC-mapped tests, failure paths, edge cases, pass/fail report. Never approves PRs, never merges.
- **Tools:** Read, Glob, Grep, Bash, Write, Edit
- **Skills loaded:** `laravel-best-practices`
- **Invokes:** `/qa-test`

### `reviewer` (Code Reviewer) 🟠
- **Charter:** review PRs, post inline feedback, request changes or approve. Never merges, never modifies code. On approve, relabels story to `state:ready-for-docs` and hands off to Docs.
- **Tools:** Read, Glob, Grep, Bash
- **Skills loaded:** `laravel-best-practices`
- **Invokes:** `/review`

### `docs` (Documentation Writer) 🔴
- **Charter:** mandatory chain step between Reviewer approval and human merge. Writes user-facing guides (EN + AR) on GitHub Pages, maintains `CHANGELOG.md`. Never modifies PHP/Vue source, never merges. The PRD cannot close until every story passes through this step.
- **Tools:** Read, Glob, Grep, Bash, Write, Edit
- **Skills loaded:** `press-release`, `eol-message`, `tailwindcss-development`
- **Invokes:** `/docs-feature` (chain mode — pushes to the story's PR branch), `/docs-guide` (standalone guide PR), `/docs-changelog` (cut a release)

## Memory

Each agent has a project-scoped memory file at `.claude/agent-memory/<agent>/MEMORY.md`. The first ≤200 lines are auto-injected into the agent's system prompt at session start. Agents are instructed to read it before non-trivial tasks and append concise notes after.

These files are **committed to git** so institutional knowledge follows the open-source repo.

## Adding a new agent

1. Drop `.claude/agents/<name>.md` with the canonical 7-section system prompt structure (see existing agents for examples).
2. Seed `.claude/agent-memory/<name>/MEMORY.md` with relevant priors.
3. Add `.claude/commands/<command>.md` for any slash commands.
4. Add an `agent:<name>` label via `gh label create` (and update `setup.sh`).
5. Update this file and `workflow.md` to document where the agent fits in the chain.

## Agent vs skill vs slash command — quick reminder

- **Skills** (`.claude/skills/<name>/SKILL.md`) — playbooks/procedures. Loaded into an agent's context via the `skills:` frontmatter field.
- **Subagents** (`.claude/agents/<name>.md`) — personas with isolated context, tool allowlist, model, memory. Invoked from the main session.
- **Slash commands** (`.claude/commands/<name>.md`) — shortcut prompts that delegate to subagents. No logic of their own.
