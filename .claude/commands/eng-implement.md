---
description: Engineer agent branches off 1.x, implements the story, runs Pint + tests, opens a PR.
argument-hint: <story-issue-number>
---

Use the **engineer** subagent to implement issue #$ARGUMENTS.

The Engineer should:
- Read issue #$ARGUMENTS, AC, UX flow, and Tech Lead design.
- Confirm `state:ready-for-impl` and a Tech Lead design comment exist; otherwise push back and stop.
- Branch: `git checkout 1.x && git pull && git checkout -b feat/<short-kebab-name>`.
- Implement following the design's "Files to touch" exactly.
- Add at least one happy-path PHPUnit feature test.
- Run `vendor/bin/pint --dirty --format agent`.
- Run `php artisan test --compact tests/Feature/<Name>Test.php`.
- Regenerate Wayfinder if controllers/routes changed.
- Commit with `Refs #$ARGUMENTS` (no `--no-verify`, no `--amend`, no `--force`).
- Push and `gh pr create --base 1.x --label "state:in-review,agent:engineer"` with `Closes #$ARGUMENTS` in the body.
- Relabel the issue to `state:in-review,agent:engineer`.
- Return the PR URL and the structured Output block.

After the Engineer finishes, ask me whether to proceed to `/qa-test <pr#>`.
