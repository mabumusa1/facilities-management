---
description: PM agent drafts a PRD as a GitHub issue using the prd.yml template.
argument-hint: <topic>
---

Use the **pm** subagent to draft a PRD for: $ARGUMENTS

The PM agent should:
- Run the `prd-development` skill end-to-end.
- File the PRD via `gh issue create --template prd.yml --title "PRD: <name>" --label "type:prd,state:draft,area:<inferred>,agent:pm"`.
- Return the issue URL and the structured Output block.

After the PM agent finishes, summarize the PRD URL and pause for my review before splitting into stories.
