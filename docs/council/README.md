# Product Council

A council of eight Claude Code subagents that collaborate to build features for this Laravel real estate management platform. GitHub is the only system of record — all work flows through issues, PRs, labels, and a Projects v2 board.

The council is **human-in-the-loop only**: nothing fires automatically. You invoke an agent from an interactive Claude Code session, review the result, and invoke the next.

## The agents

| Agent | Owns | Color |
|---|---|---|
| `pm` | Discovery, PRDs, epics, user stories, prioritization | 🔵 blue |
| `tech-lead` | Technical designs, architecture, decomposition | 🟣 purple |
| `designer` | UX flows, wireframes (markdown/ASCII), microcopy, a11y, RTL/Arabic | 🩷 pink |
| `delivery-pm` | Project board, milestones, sprint planning, status, unblocking | 🩵 cyan |
| `engineer` | Code implementation, PRs, happy-path tests | 🟢 green |
| `qa` | AC-mapped tests, failure paths, edge cases, validation reports | 🟡 yellow |
| `reviewer` | Code review, security, conventions, architecture critique | 🟠 orange |
| `docs` | User-facing guides (GitHub Pages), CHANGELOG, bilingual EN/AR | 🔴 red |

Definitions: `.claude/agents/<name>.md`. Each has its own system prompt, tool allowlist, and project-scoped memory at `.claude/agent-memory/<name>/MEMORY.md` (committed to git so institutional knowledge follows the repo).

## How a feature flows

```
PM ─→ PM ─→ Designer ─→ Tech Lead ─→ Engineer ─→ QA ─→ Reviewer ─→ Docs ─→ (human merges) ─→ Delivery PM
PRD   stories  UX flow    design       PR         tests   review    guide+CHANGELOG
```

Docs is a **mandatory chain step**: the PRD cannot close until every one of its stories has shipped user-facing documentation on the same PR branch. See [`workflow.md`](./workflow.md) for the full lifecycle.

## Quick start

1. **Authenticate `gh`** (one time, with `project` scope):
   ```bash
   gh auth login
   gh auth refresh -s project
   gh repo set-default
   ```
2. **Run setup** (creates labels, project, fields — idempotent):
   ```bash
   bash docs/council/setup.sh
   ```
3. **Manual one-time UI step** — create the saved project views per [`project-views.md`](./project-views.md). The `gh` CLI does not expose project view creation cleanly.
4. **Update `.github/ISSUE_TEMPLATE/config.yml`** — replace `OWNER/REPO` placeholders with your actual repo path.
5. **Use the council:**
   ```
   /feature <topic>                    # full chain with checkpoints
   /pm-prd <topic>                     # PM agent only
   /handoff <issue#>                   # auto-route to the right next agent
   ```
   See [`agents.md`](./agents.md) for all 14 slash commands.

6. **Enable GitHub Pages** (one time) — repo Settings → Pages → Source: `main` branch, `/docs` folder. The Docs agent publishes user guides and release notes there. `docs/council/` is excluded by `docs/_config.yml`.

## Hard rules

- **Reviewer never merges.** Humans always merge.
- **Docs never merges.** Docs commits to the PR branch; humans merge.
- **PM never writes code.** Tech Lead never opens PRs. Engineer never approves own PR. QA never merges. Delivery PM never modifies issue bodies. Docs never modifies PHP/Vue source.
- **PRD cannot close without docs.** Every story in a PRD must pass through the Docs step before its PR merges.
- **Council process artifacts live in GitHub or `.claude/agent-memory/`.** Do not create ad-hoc markdown for PRDs/designs in `docs/`. **Exception:** user-facing documentation under `docs/guides/`, `docs/ar/`, `docs/index.md`, `docs/changelog.md`, and `docs/_config.yml` is the published help center and is maintained by the Docs agent.
- **Each agent updates only its own memory directory.**

## Why this design

- **Subagents (not skills, not agent teams):** subagents have their own context window, tool allowlist, model, and `skills:` frontmatter. The seven roles benefit from isolation (Reviewer shouldn't have Write tools; PM shouldn't see implementation diffs polluting context). Agent teams target autonomous cross-session coordination, which we explicitly don't want.
- **Conductor pattern (not subagent-to-subagent):** the Claude Code platform documents that subagents cannot invoke each other. The main session is the conductor — it pipes one agent's structured Output block into the next. The user authorizes every handoff.
- **GitHub as system of record (not a database, not Slack):** every artifact is an issue, comment, PR, or project field. Anyone can audit the full feature lineage in GitHub UI without running anything.
- **Project-scoped memory:** `memory: project` writes to `.claude/agent-memory/<agent>/` which is committed to git. The repo carries its own institutional knowledge. Anyone cloning gets the agents *and* their accumulated learning.

## Open source

This is the open-source product. Contribute by:
- Improving an existing agent's system prompt.
- Adding a new agent persona (drop `.claude/agents/<name>.md`).
- Refining an issue template.
- Sharing your `MEMORY.md` learnings (where they are universally useful — not project-specific facts).

## Reuses

- The 23 PM skills under `.claude/skills/` (loaded into agents via the `skills:` frontmatter).
- Laravel Boost MCP for the engineering agents.
- The official `gh` CLI for all GitHub interaction — no GitHub Actions, no webhooks, no custom backend.
