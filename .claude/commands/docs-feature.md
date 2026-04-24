---
description: Docs agent writes a user guide + CHANGELOG entry on the story PR branch (chain mode — mandatory step between Reviewer approval and human merge).
argument-hint: <story-issue-number>
---

Use the **docs** subagent to write user-facing documentation for story #$ARGUMENTS, in chain mode.

Precondition: issue #$ARGUMENTS must have label `state:ready-for-docs` (set by Reviewer after approval). If it doesn't, abort and report the current state.

The Docs agent should:
- Confirm `state:ready-for-docs` on issue #$ARGUMENTS. If absent, abort.
- Find the linked PR (from the story body's `Closes #N` references or `gh pr list --search "linked:issue #$ARGUMENTS"`).
- `gh pr checkout <pr#>` to switch to the PR branch locally.
- Read the story, UX comment, tech design, QA report, and `gh pr diff <pr#>` for real context.
- Determine if the change is user-visible. If internal-only, write only a `_Internal_` CHANGELOG line; skip the guide.
- Write or update `docs/guides/<area>/<slug>.md` (EN) and `docs/ar/guides/<area>/<slug>.md` (AR) per the guide anatomy in `.claude/agents/docs.md`.
- Add a bullet to `CHANGELOG.md` under `## [Unreleased]` in the correct Keep-a-Changelog section, linking the PR.
- Append a user-friendly line to `docs/changelog.md`.
- Commit and push to the **existing PR branch** (not a new branch).
- Relabel: `--add-label "state:ready-to-merge,agent:docs" --remove-label "state:ready-for-docs,agent:reviewer"`.
- Comment on the PR: `📘 Docs added: <paths>`.
- Return the commit URL, the docs paths, and the structured Output block.

Do not modify PHP or Vue source. Do not open a new PR. Do not merge.

After the Docs agent finishes, tell me the story is `state:ready-to-merge` — human merges next.
