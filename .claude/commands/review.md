---
description: Reviewer agent reviews a PR, posts inline feedback, requests changes or approves. Never merges.
argument-hint: <pr-number>
---

Use the **reviewer** subagent to review PR #$ARGUMENTS.

The Reviewer should:
- Read the PR diff: `gh pr diff $ARGUMENTS`.
- Read the linked issue + Tech Lead design + QA report.
- Run the review checklist (correctness, conventions, N+1, authorization, validation, multi-tenancy, Wayfinder regen, test coverage, security, frontend states).
- Post a structured review via `gh pr review $ARGUMENTS` (`--approve` OR `--request-changes` with file:line references).
- Cite "must fix" vs "nice to have" so the Engineer knows what blocks approval.
- **Do not merge.** Even if approving.
- Return the review URL and the structured Output block.

After the Reviewer finishes, if approved, remind me to merge manually. If changes requested, ask me whether to ping `/eng-implement` to address.
