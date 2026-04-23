---
description: QA agent adds AC-mapped tests to a PR branch, runs the suite, posts a pass/fail report per AC.
argument-hint: <pr-number>
---

Use the **qa** subagent to add tests and validate PR #$ARGUMENTS.

The QA agent should:
- Read the PR and the linked issue (from `Closes #N` in the PR body).
- Check out the PR branch via `gh pr checkout $ARGUMENTS`.
- Identify the gap between existing tests and the issue's AC.
- Add tests for each uncovered AC, plus at least one failure path and one edge case.
- Run only the affected tests: `php artisan test --compact tests/Feature/<Name>Test.php`.
- Run `vendor/bin/pint --dirty --format agent` on any modified PHP files.
- Commit and push to the PR branch (do NOT change the base branch).
- Post the AC-mapped report as a PR comment (table: AC | Status | Test).
- Return the comment URL and the structured Output block.

After QA finishes, ask me whether to proceed to `/review $ARGUMENTS` (if green) or notify the Engineer (if red).
