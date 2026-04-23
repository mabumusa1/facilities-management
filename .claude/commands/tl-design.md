---
description: Tech Lead agent posts a technical design as a comment on a story issue.
argument-hint: <story-issue-number>
---

Use the **tech-lead** subagent to write a technical design on issue #$ARGUMENTS.

The Tech Lead should:
- Read issue #$ARGUMENTS, the user story, AC, and any prior PM/UX comments.
- Map every AC bullet to the affected models, controllers, routes, Inertia pages, and Wayfinder regen needs.
- Run `laravel-best-practices` on the proposed approach (N+1, validation, authorization, queue/scheduler, multi-tenancy).
- Post the tech design as a comment with sections: Approach, Files to touch, Data model changes, API/contract changes, Risks, Test plan.
- Relabel to `state:ready-for-impl,agent:tech-lead` (removing `state:ready-for-design`).
- Return the comment URL and the structured Output block.

After the Tech Lead finishes, ask me whether to proceed to `/eng-implement $ARGUMENTS`.
