---
description: "Run strict docs/pages parity implementation for a feature folder"
name: "Pages Parity"
argument-hint: "Example: docs/pages/contacts-tenant-form"
agent: "pages-parity-builder"
model: ["GPT-5 (copilot)", "Claude Sonnet 4.5 (copilot)"]
---

Implement or fix the feature to match captured evidence exactly.

Use the user-provided argument as the target folder (or folders) under docs/pages.

Required behavior:
- Run all command-line operations via Laravel Sail only (`./vendor/bin/sail ...`).
- Parse API contracts from api/endpoints.json.
- Match fields, payload keys, response keys, and values exactly.
- Match UI structure and visible labels from screenshots and snapshot.yml.
- Reproduce empty, loading, error, and not-found states shown in captures.
- Keep edits minimal and aligned with existing Laravel + Inertia architecture.
- Add or update tests that lock parity-critical behavior.
- Run the smallest relevant test subset and report results.

Output format:
1. Target folder(s) used.
2. Mismatches found.
3. Files changed.
4. Tests run and results.
5. Remaining gaps due to missing or conflicting evidence.
