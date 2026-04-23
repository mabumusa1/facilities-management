---
description: Designer agent adds a UX flow + wireframes + microcopy + a11y notes as a comment on a story issue.
argument-hint: <story-issue-number>
---

Use the **designer** subagent to add a UX-flow comment on issue #$ARGUMENTS.

The Designer should:
- Read issue #$ARGUMENTS and any existing comments.
- Inspect sibling pages in `resources/js/pages/<area>/` to align with existing component patterns.
- Post the UX-flow comment matching the `ux-flow.yml` body shape (User flow, Key screens, Microcopy, States, Accessibility, RTL notes).
- Relabel to `state:ready-for-design,agent:designer` (removing `state:ready-for-ux`).
- Return the comment URL and the structured Output block.

After the Designer finishes, ask me whether to proceed to `/tl-design $ARGUMENTS`.
