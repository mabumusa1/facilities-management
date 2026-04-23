---
description: Conductor — runs the full council chain (PRD → stories → UX → design → impl → QA → review) with checkpoints.
argument-hint: <feature topic>
---

You are conducting a full feature build for: **$ARGUMENTS**

Run this chain end-to-end. **Pause for my approval at every CHECKPOINT.** Pipe each subagent's structured Output block (artifact URLs, new state label) into the next subagent's prompt as context.

**The chain:**

1. Use the **pm** subagent to draft a PRD for "$ARGUMENTS" via `/pm-prd` flow. File via `prd.yml` template, label `type:prd,state:draft,area:<inferred>,agent:pm`.
   - **CHECKPOINT:** Show the PRD URL and a one-paragraph excerpt. Ask: "Approve this PRD? (yes / tweak: <feedback> / abort)"

2. Use the **pm** subagent to split the approved PRD into user-story issues via `/pm-stories <prd#>`.
   - **CHECKPOINT:** Show all story URLs with one-line summaries. Ask: "Which stories should advance to UX/design? (e.g., '#42, #43' / all / abort)"

3. **For each approved story** (process serially, not in parallel — UX/design comments accumulate on the same issue):
   a. If the story's `area:*` is in {`properties`, `leasing`, `marketplace`, `facilities`, `communication`, `admin`, `reports`, `settings`, `documents`}, use the **designer** subagent via `/design-flow <story#>`.
      - **CHECKPOINT:** Show the UX comment URL. Ask: "Approve UX flow? (yes / tweak / skip designer / abort)"
   b. Use the **tech-lead** subagent via `/tl-design <story#>` to add the technical design.
      - **CHECKPOINT:** Show the design comment URL. Ask: "Approve design? (yes / tweak / abort)"
   c. Use the **engineer** subagent via `/eng-implement <story#>` to implement and open a PR.
      - **CHECKPOINT:** Show the PR URL. Ask: "Continue to QA? (yes / hold / abort)"
   d. Use the **qa** subagent via `/qa-test <pr#>` to add tests and validate AC.
      - If QA is red, ask whether to ping `/eng-implement` for fixes.
   e. Use the **reviewer** subagent via `/review <pr#>` to post a code review.
      - **CHECKPOINT:** Show the review URL and verdict. Ask: "Merge now? (yes / hold for fixes / abort)"

4. After all stories are merged, use the **delivery-pm** subagent to update the project board for every touched issue and report sprint impact.

**Conducting rules:**
- Never invoke the next agent without my explicit approval at each CHECKPOINT.
- If any step returns an error, stop and surface it — do not paper over.
- If at any point I say "abort", stop the chain. Issues already filed remain.
- Carry the prior agent's structured Output block forward verbatim — do not paraphrase artifact URLs.
