---
description: Delivery PM proposes contents for a milestone using the prioritization-advisor skill.
argument-hint: <milestone-name>
---

Use the **delivery-pm** subagent to propose contents for milestone "$ARGUMENTS".

The Delivery PM should:
- List candidate issues: `gh issue list --label "state:ready-for-design,state:ready-for-impl" --state open --json number,title,labels --limit 100`.
- Run the `prioritization-advisor` skill against the candidates.
- Output a proposal table: `# | Title | Type | Area | Priority | Why now`.
- **Do not assign milestones automatically** — wait for my approval before running `gh issue edit <N> --milestone "$ARGUMENTS"`.
- Return the structured Output block.
