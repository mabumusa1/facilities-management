---
description: Look up an issue's current state label and dispatch to the correct next agent.
argument-hint: <issue-number>
---

Look up the current `state:*` label on issue #$ARGUMENTS and dispatch to the correct next agent.

1. Run `gh issue view $ARGUMENTS --json labels,title,number`.
2. Find the `state:*` label. Route as follows:
   - `state:draft` → use **pm** subagent via `/pm-stories $ARGUMENTS` (PRD ready to split).
   - `state:ready-for-ux` → use **designer** subagent via `/design-flow $ARGUMENTS`.
   - `state:ready-for-design` → use **tech-lead** subagent via `/tl-design $ARGUMENTS`.
   - `state:ready-for-impl` → use **engineer** subagent via `/eng-implement $ARGUMENTS`.
   - `state:in-progress` → report "issue is in progress with engineer; nothing to dispatch."
   - `state:in-review` → check if there is a linked PR; if yes, use **qa** subagent via `/qa-test <pr#>` then **reviewer** via `/review <pr#>`.
   - `state:ready-for-docs` → use **docs** subagent via `/docs-feature <issue#>` (chain-mode — pushes docs onto the existing PR branch).
   - `state:ready-to-merge` → report "docs committed; human merges the PR next. Nothing to dispatch."
   - `state:blocked` → use **delivery-pm** subagent to surface the blocker.
   - `state:done` → report "issue already done; nothing to dispatch."
3. If no `state:*` label is set, ask me which state the issue is in and whether to set it.
4. Pause for my approval before invoking the dispatched agent — show what you will do and the issue context.
