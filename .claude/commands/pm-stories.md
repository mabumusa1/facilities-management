---
description: PM agent splits a PRD into user-story issues.
argument-hint: <prd-issue-number>
---

Use the **pm** subagent to split PRD #$ARGUMENTS into user-story issues.

The PM agent should:
- Read PRD #$ARGUMENTS via `gh issue view`.
- Run the `user-story` and `user-story-splitting` skills.
- File one issue per story via `gh issue create --template user-story.yml --label "type:story,state:ready-for-ux,area:<same-as-prd>,agent:pm"`.
- Each story body must include `Part of #$ARGUMENTS` so GitHub auto-links it to the PRD.
- Return the list of story URLs and the structured Output block.

After the PM agent finishes, list the story URLs and ask me which to advance next (`/design-flow <n>` or `/tl-design <n>` if no UI scope).
