---
description: Delivery PM reports current project board status, sprint health, and blockers.
---

Use the **delivery-pm** subagent to report current project status.

The Delivery PM should:
- Query the Product Council project: `gh project item-list <#> --owner @me --format json --limit 200`.
- Group by Status; report counts per status, top items by Priority, blockers (state `blocked`).
- Compute agent load (count per `agent:*` label); flag overloaded (>5) or idle (=0).
- Output the report as a markdown table in the response (do not file as a comment unless I ask).
- Return the structured Output block.
