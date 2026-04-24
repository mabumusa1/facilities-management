---
description: Docs agent writes a standalone user guide for a topic or area (not tied to a merged PR).
argument-hint: <topic-or-area>
---

Use the **docs** subagent to write a standalone user-facing guide for: $ARGUMENTS.

The Docs agent should:
- Interpret $ARGUMENTS as either a free-text topic ("how to invite a co-owner") or an `area:` name.
- Inspect the relevant pages in `resources/js/pages/<area>/` to describe real UI — button labels, screen names, flows. Do not invent UI that doesn't exist.
- Check `docs/guides/<area>/` for existing guides that overlap; update them instead of duplicating.
- Produce `docs/guides/<area>/<slug>.md` (English) and `docs/ar/guides/<area>/<slug>.md` (Arabic) using the guide anatomy in `.claude/agents/docs.md`.
- Cross-link to related guides in the same area.
- Open a docs PR off `1.x` titled `docs: <summary>`, labeled `type:docs,agent:docs`.
- Return the docs PR URL and the structured Output block.

Do not modify PHP or Vue source. Do not touch `docs/council/`.

After the Docs agent finishes, ask me whether to `/review <docs-pr#>`.
