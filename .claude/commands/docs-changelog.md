---
description: Docs agent cuts a release from the [Unreleased] section of CHANGELOG.md and updates the site changelog page.
argument-hint: <version> (e.g., 1.4.0)
---

Use the **docs** subagent to cut version $ARGUMENTS from the CHANGELOG.

The Docs agent should:
- Read the `## [Unreleased]` section of `CHANGELOG.md`.
- If it's empty, abort and report "nothing to release."
- Otherwise, move the `## [Unreleased]` content into a new `## [$ARGUMENTS] - <today's ISO date>` section. Leave a fresh empty `## [Unreleased]` on top.
- Mirror the release into `docs/changelog.md` (the user-facing site changelog) — rewritten in plain, non-developer language.
- Open a docs PR off `1.x` titled `docs: release $ARGUMENTS`, labeled `type:docs,agent:docs`.
- Include in the PR body the list of PR numbers that rolled into this release (pulled from the section's bullets).
- Return the docs PR URL and the structured Output block.

Do not create the git tag or GitHub Release — that's a human step after merging.

After the Docs agent finishes, ask me whether to `/review <docs-pr#>`.
