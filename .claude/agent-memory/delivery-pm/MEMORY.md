# Delivery PM — Agent Memory

Append concise notes as you learn. Keep this under 200 lines via curation.

## Project board
- Owner: `mabumusa1` (user scope)
- Title: `@mabumusa1's Pillar One`
- Project number: 4
- Project ID (GraphQL node): PVT_kwHOAMCuys4BVdbX
- URL: https://github.com/users/mabumusa1/projects/4

**HARD RULE (user preference, 2026-04-24):** Never use project #5 ("Product Council", ID `PVT_kwHOAMCuys4BVilj`). The only council board is **project #4**. If you find items auto-attached to #5, remove them (`gh project item-delete 5 --owner mabumusa1 --id <item-id>`). Never add items to #5, never sync fields on #5, never even read from #5 unless cleaning it up.

## Field IDs (cached — no lookups needed at runtime)

### Status (`PVTSSF_lAHOAMCuys4BVdbXzhQ5F0g`) — single-select
| Option name | Option ID |
|---|---|
| Backlog | `f75ad846` |
| Ready | `08afe404` |
| In progress | `47fc9ee4` |
| In review | `4cc61d42` |
| Done | `98236657` |
| Blocked | `26c9bae6` |

### Priority (`PVTSSF_lAHOAMCuys4BVdbXzhQ5GJI`) — single-select
| Option name | Option ID |
|---|---|
| P0 | `79628723` |
| P1 | `0a877460` |
| P2 | `da944a9c` |
| P3 | `12a1731d` |

### Iteration (`PVTIF_lAHOAMCuys4BVdbXzhQ5GJU`) — iteration field
- Duration: 14 days (2-week sprints)
- Start day: Monday
- Iterations (as of 2026-04-23):
  - Iteration 1 — `381c7c80` — starts 2026-04-23
  - Iteration 2 — `54cf5c95` — starts 2026-05-07
  - Iteration 3 — `d2c335bc` — starts 2026-05-21

This is the "Sprint" field the council's plan referenced.

### Size (`PVTSSF_lAHOAMCuys4BVdbXzhQ5GJM`) — single-select, unused by council
Options: XS, S, M, L, XL. Built into the Pillar One template; ignore unless estimation becomes useful.

### Type (`PVTSSF_lAHOAMCuys4BVdbXzhQ5HL0`) — single-select
| Option name | Option ID |
|---|---|
| prd | `e840f539` |
| epic | `3a82d569` |
| story | `e9f8047e` |
| design | `f86052b4` |
| ux-flow | `5de8f6e7` |
| task | `6887a4d6` |
| bug | `dd4cc247` |

### Area (`PVTSSF_lAHOAMCuys4BVdbXzhQ5HOE`) — single-select
| Option name | Option ID |
|---|---|
| properties | `a1a25242` |
| leasing | `d139d90d` |
| marketplace | `57748133` |
| facilities | `f2141a90` |
| service-requests | `1e0c39dc` |
| accounting | `d9ce1d84` |
| communication | `ab3fb780` |
| admin | `216dddeb` |
| reports | `cdb1bf52` |
| settings | `24dcc078` |
| auth | `0cf7602e` |
| visitor-access | `121c3305` |
| documents | `b8cde25e` |
| contacts | `cf174ee7` |

### Agent (`PVTSSF_lAHOAMCuys4BVdbXzhQ5HSQ`) — single-select
| Option name | Option ID |
|---|---|
| pm | `cc230105` |
| tech-lead | `346a8576` |
| designer | `ae5d3a11` |
| delivery-pm | `783a5428` |
| engineer | `c17bf153` |
| qa | `6c802ef9` |
| reviewer | `6cc24faf` |

## Label → project field mapping
| `state:` label | Status option | Status option ID |
|---|---|---|
| `state:draft` | Backlog | `f75ad846` |
| `state:ready-for-ux` | Ready | `08afe404` |
| `state:ready-for-design` | Ready | `08afe404` |
| `state:ready-for-impl` | Ready | `08afe404` |
| `state:in-progress` | In progress | `47fc9ee4` |
| `state:in-review` | In review | `4cc61d42` |
| `state:blocked` | Blocked | `26c9bae6` |
| `state:done` | Done | `98236657` |

`type:*`, `area:*`, `priority:*` (p0/p1/p2/p3), `agent:*` labels map 1:1 to the respective fields above.

## Saved views (confirmed on project #4)
- `By Status` — board layout, grouped by Status (primary kanban)
- `By Area` — table layout, grouped by Area
- `By Sprint` — table layout, grouped by Iteration
- `By Agent` — table layout, grouped by Agent
- `Blocked` — table layout filtered to `Status = Blocked`
- Pillar One built-ins kept alongside: `Prioritized backlog`, `Status board`, `Roadmap`, `Bugs 🐛`, `In review`, `My items`

## Canonical gh commands for this project

```bash
# Add an issue to the project (idempotent)
gh project item-add 4 --owner mabumusa1 --url <issue-url>

# Edit an item's single-select field
gh project item-edit \
  --project-id PVT_kwHOAMCuys4BVdbX \
  --id <item-id> \
  --field-id PVTSSF_lAHOAMCuys4BVdbXzhQ5F0g \
  --single-select-option-id 47fc9ee4   # e.g., set Status = "In progress"

# List items
gh project item-list 4 --owner mabumusa1 --format json --limit 200
```

## Sprint cadence
- 2-week iterations (Monday-start), Pillar One defaults. Three iterations pre-created.

## Blocker patterns
_(append as you encounter: "Issues blocked waiting on X happen every sprint — propose a standing solution")_

## Agent load heuristics
- More than 5 open items under `agent:<name>` → overloaded (suggest redistribution or sprint trim).
- Zero items under `agent:<name>` for >1 sprint → under-utilized (suggest surfacing work).

## Past work index
_(append one line per sprint plan or status report: `2026-MM-DD status — <N committed / M delivered / K blocked>`)_
- 2026-04-24 sync — #130 (PRD: Admin Audit Log) + #131 (Story: RBAC audit log) added to project #4; fields synced (Status, Type, Area=admin, Agent=pm); Priority + Iteration left unset pending sprint planning.
- 2026-04-24 sync — Batch 1 PRDs #132-#136 added to project #4 (Phase 2 of backlog buildout); Status=Backlog, Type=prd, Area per issue, Agent=pm; Priority + Iteration left unset. Project #5 was empty — no cleanup needed.
- 2026-04-24 sync — Batch 2 PRDs #137-#141 added to project #4 (documents, marketplace, facilities, communication, visitor-access); Status=Backlog, Type=prd, Area per issue, Agent=pm; Priority + Iteration left unset. Project #5 clean — no cleanup needed.
- 2026-04-24 sync — Batch 3 PRDs #142-#146 added to project #4 (settings, auth, admin, reports x2); Phase 2 complete — 15 PRDs (#132-#146) filed and synced to project #4. Project #5 clean — no cleanup needed.
- 2026-04-24 sync — Phase 3 session 1 stories #147-#168 (22 stories: 11 Contacts, 11 Properties) synced to project #4 with Status=Ready, Type=story, Area per PRD, Agent=pm.

## Environment notes
- **jq is NOT installed in this shell.** Never pipe `gh ... --format json | jq '...'` — it will fail with `/bin/bash: jq: command not found`. Always use the built-in `--jq` flag on `gh` commands (e.g. `gh project item-list 4 --owner mabumusa1 --format json --jq '.items[] | ...'`). Works identically for filtering and produces text or JSON output.
