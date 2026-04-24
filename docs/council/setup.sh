#!/usr/bin/env bash
# Product Council — one-shot setup.
# Idempotent: safe to re-run. Skips anything that already exists.
#
# Prerequisites:
#   - gh CLI installed
#   - gh auth login (with `project` scope: gh auth refresh -s project)
#   - gh repo set-default
#
# After running, manually create the saved project views per docs/council/project-views.md.

set -euo pipefail

# -- helpers ------------------------------------------------------------------

color_reset='\033[0m'
color_blue='\033[1;34m'
color_green='\033[1;32m'
color_yellow='\033[1;33m'

info()  { printf "${color_blue}[setup]${color_reset} %s\n" "$*"; }
ok()    { printf "${color_green}[ ok  ]${color_reset} %s\n" "$*"; }
skip()  { printf "${color_yellow}[skip ]${color_reset} %s\n" "$*"; }
die()   { printf "[fail] %s\n" "$*" >&2; exit 1; }

require() { command -v "$1" >/dev/null 2>&1 || die "missing command: $1"; }

require gh

gh auth status >/dev/null 2>&1 || die "gh not authenticated. Run: gh auth login && gh auth refresh -s project"

# JSON parsing uses gh's built-in --jq flag (gojq bundled in the gh binary).
# No external jq required.

REPO=$(gh repo view --json nameWithOwner -q .nameWithOwner 2>/dev/null) || die "no default repo set. Run: gh repo set-default"
OWNER=$(echo "$REPO" | cut -d/ -f1)
info "Setting up Product Council in $REPO"

# -- labels -------------------------------------------------------------------

create_label() {
  local name="$1" color="$2" desc="${3:-}"
  if gh label list --limit 200 --json name -q '.[].name' | grep -Fxq "$name"; then
    skip "label '$name'"
  else
    gh label create "$name" --color "$color" --description "$desc" >/dev/null
    ok "label '$name'"
  fi
}

info "Creating labels…"

# type:
create_label "type:prd"        "0E8A16" "Product Requirements Document"
create_label "type:epic"       "5319E7" "Hypothesis-level grouping of stories"
create_label "type:story"      "1D76DB" "A single deliverable user story"
create_label "type:design"     "5319E7" "Standalone tech design"
create_label "type:ux-flow"    "FBCA04" "Standalone UX flow"
create_label "type:task"       "C5DEF5" "Maintenance, refactor, infra"
create_label "type:bug"        "D93F0B" "Defect"
create_label "type:docs"       "006B75" "User-facing documentation / changelog"

# area:
for area in properties leasing marketplace facilities service-requests accounting communication admin reports settings auth visitor-access documents contacts; do
  create_label "area:$area" "BFD4F2" "Domain: $area"
done

# state:
create_label "state:draft"             "FBCA04" "Author still working"
create_label "state:ready-for-ux"      "FEF2C0" "Ready for Designer"
create_label "state:ready-for-design"  "FEF2C0" "Ready for Tech Lead"
create_label "state:ready-for-impl"    "FEF2C0" "Ready for Engineer"
create_label "state:in-progress"       "1D76DB" "Engineer working"
create_label "state:in-review"         "5319E7" "PR open, QA + Reviewer act"
create_label "state:ready-for-docs"    "FEF2C0" "Reviewer approved — Docs's turn"
create_label "state:ready-to-merge"    "0EA5E9" "Docs committed — human merges next"
create_label "state:blocked"           "D93F0B" "Stuck — see comments"
create_label "state:done"              "0E8A16" "Merged"

# priority:
create_label "priority:p0" "B60205" "Outage / security / data loss"
create_label "priority:p1" "D93F0B" "Broken core flow"
create_label "priority:p2" "FBCA04" "Important, not urgent"
create_label "priority:p3" "C5DEF5" "Nice to have"

# agent:
create_label "agent:pm"          "1D76DB" "Last touched by PM"
create_label "agent:tech-lead"   "5319E7" "Last touched by Tech Lead"
create_label "agent:designer"    "F9A8D4" "Last touched by Designer"
create_label "agent:delivery-pm" "0EA5E9" "Last touched by Delivery PM"
create_label "agent:engineer"    "0E8A16" "Last touched by Engineer"
create_label "agent:qa"          "FBCA04" "Last touched by QA"
create_label "agent:reviewer"    "FB8C00" "Last touched by Reviewer"
create_label "agent:docs"        "B91C1C" "Last touched by Docs"

# -- project ------------------------------------------------------------------

if [[ -n "${COUNCIL_PROJECT_NUMBER:-}" ]]; then
  PROJECT_NUMBER="$COUNCIL_PROJECT_NUMBER"
  info "Using existing project #$PROJECT_NUMBER (COUNCIL_PROJECT_NUMBER override)"
  gh project view "$PROJECT_NUMBER" --owner "@me" --format json --jq .id >/dev/null 2>&1 \
    || die "project #$PROJECT_NUMBER not found for owner @me"
else
  info "Looking for existing 'Product Council' project…"
  PROJECT_NUMBER=$(gh project list --owner "@me" --format json --limit 50 --jq '.projects[] | select(.title=="Product Council") | .number' | head -n1)

  if [[ -z "$PROJECT_NUMBER" ]]; then
    info "Creating project 'Product Council'…"
    PROJECT_NUMBER=$(gh project create --owner "@me" --title "Product Council" --format json --jq .number)
    ok "project #$PROJECT_NUMBER created"
  else
    skip "project #$PROJECT_NUMBER already exists"
  fi
fi

PROJECT_ID=$(gh project view "$PROJECT_NUMBER" --owner "@me" --format json --jq .id)

# -- project fields -----------------------------------------------------------

create_field() {
  local name="$1" data_type="$2" options="${3:-}"
  if gh project field-list "$PROJECT_NUMBER" --owner "@me" --format json --jq '.fields[].name' | grep -Fxq "$name"; then
    skip "field '$name'"
    return
  fi
  if [[ -n "$options" ]]; then
    gh project field-create "$PROJECT_NUMBER" --owner "@me" --name "$name" --data-type "$data_type" --single-select-options "$options" >/dev/null
  else
    gh project field-create "$PROJECT_NUMBER" --owner "@me" --name "$name" --data-type "$data_type" >/dev/null
  fi
  ok "field '$name'"
}

info "Creating project fields…"
create_field "Type"     SINGLE_SELECT "prd,epic,story,design,ux-flow,task,bug"
create_field "Area"     SINGLE_SELECT "properties,leasing,marketplace,facilities,service-requests,accounting,communication,admin,reports,settings,auth,visitor-access,documents,contacts"
create_field "Agent"    SINGLE_SELECT "pm,tech-lead,designer,delivery-pm,engineer,qa,reviewer"
# Note: Priority is skipped here because GitHub's "Pillar One" template (and many others)
# ship with a Priority single-select pre-populated (P0, P1, P2). If your project has no
# Priority field, create it manually in the UI with options p0, p1, p2, p3 — there is no
# GraphQL mutation to add options to an existing single-select, so we avoid mismatch.
# Note: the default "Status" field ships with options that vary by project template.
# Pillar One ships with: Backlog, Ready, In progress, In review, Done (no Blocked).
# Edit options in the UI if you want exact alignment with the council's state:* labels.
# Note: "Sprint" (iteration) fields cannot be created via gh CLI — create manually in UI.
# Pillar One ships with an "Iteration" field already (14-day default). Use that as Sprint.

# -- persist project number to delivery-pm memory -----------------------------

DPM_MEMORY=".claude/agent-memory/delivery-pm/MEMORY.md"
if [[ -f "$DPM_MEMORY" ]] && grep -q "Project number:" "$DPM_MEMORY"; then
  if ! grep -q "Project number:.*$PROJECT_NUMBER" "$DPM_MEMORY"; then
    info "Updating delivery-pm memory with project number $PROJECT_NUMBER"
    sed -i.bak "s|Project number:.*|Project number: $PROJECT_NUMBER|" "$DPM_MEMORY" && rm -f "${DPM_MEMORY}.bak"
    sed -i.bak "s|Project ID (GraphQL node):.*|Project ID (GraphQL node): $PROJECT_ID|" "$DPM_MEMORY" && rm -f "${DPM_MEMORY}.bak"
    ok "delivery-pm memory updated"
  else
    skip "delivery-pm memory already has correct project number"
  fi
fi

# -- done ---------------------------------------------------------------------

cat <<EOF

${color_green}== Product Council setup complete ==${color_reset}

Project: https://github.com/users/$OWNER/projects/$PROJECT_NUMBER

Manual steps remaining (one time, none strictly required):
  1. Edit .github/ISSUE_TEMPLATE/config.yml — replace OWNER/REPO with $REPO
     (only needed if not already done).
  2. Optional — in the GitHub UI:
     a. Add "Blocked" option to the Status field (Pillar One doesn't ship with it).
     b. Add "P3" option to the Priority field if you want 4 priority levels.
     c. Create the 4 saved views per docs/council/project-views.md (no API exists).
  3. To use a different project next time: COUNCIL_PROJECT_NUMBER=<n> bash docs/council/setup.sh

Then try:
  /feature <some topic>
EOF
