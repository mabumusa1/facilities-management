# Label Taxonomy

The council uses five label namespaces. All labels are created idempotently by `setup.sh`.

## `type:` — what kind of issue this is

| Label | Color | Use |
|---|---|---|
| `type:prd` | `#0E8A16` | Product Requirements Document |
| `type:epic` | `#5319E7` | Hypothesis-level grouping of stories |
| `type:story` | `#1D76DB` | A single deliverable user story |
| `type:design` | `#5319E7` | Standalone tech design (rare — most go on the story) |
| `type:ux-flow` | `#FBCA04` | Standalone UX flow (rare — most go on the story) |
| `type:task` | `#C5DEF5` | Maintenance, refactor, infra |
| `type:bug` | `#D93F0B` | Defect |

## `area:` — which domain of the app

Mirrors the Laravel app's main areas. Use one or more, primary first.

| Label | App models / pages |
|---|---|
| `area:properties` | Community, Building, Unit |
| `area:leasing` | Lease, LeaseUnit, Resident, Owner |
| `area:marketplace` | MarketplaceUnit, MarketplaceOffer, MarketplaceVisit |
| `area:facilities` | Facility, FacilityBooking, FacilityCategory |
| `area:service-requests` | ServiceRequest, RequestCategory, RequestSubcategory |
| `area:accounting` | Transaction, Invoice, Payment, Currency |
| `area:communication` | Announcement |
| `area:admin` | Admin, Lead, AccountSubscription, AccountUser |
| `area:reports` | Reports, PowerBI |
| `area:settings` | AppSettings, FormTemplate |
| `area:auth` | Fortify (login, register, 2FA, password reset) |
| `area:visitor-access` | Visitor access |
| `area:documents` | DocumentCenter, ExcelSheet |
| `area:contacts` | Owner, Resident, Professional, Admin contacts |

## `state:` — where the issue is in the workflow

| Label | Color | Set by | Maps to project Status |
|---|---|---|---|
| `state:draft` | `#FBCA04` | PM | Backlog |
| `state:ready-for-ux` | `#FEF2C0` | PM | Ready |
| `state:ready-for-design` | `#FEF2C0` | Designer | Ready |
| `state:ready-for-impl` | `#FEF2C0` | Tech Lead | Ready |
| `state:in-progress` | `#1D76DB` | Engineer | In Progress |
| `state:in-review` | `#5319E7` | Engineer | In Review |
| `state:blocked` | `#D93F0B` | Any agent (+ comment) | Blocked |
| `state:done` | `#0E8A16` | (auto on PR merge) | Done |

## `priority:`

| Label | Color | Meaning |
|---|---|---|
| `priority:p0` | `#B60205` | Outage, security, data loss — drop everything |
| `priority:p1` | `#D93F0B` | Broken core flow — this sprint |
| `priority:p2` | `#FBCA04` | Important, not urgent — next sprint |
| `priority:p3` | `#C5DEF5` | Nice to have |

## `agent:` — which agent last touched the issue (audit trail)

| Label | Color |
|---|---|
| `agent:pm` | `#1D76DB` (blue) |
| `agent:tech-lead` | `#5319E7` (purple) |
| `agent:designer` | `#F9A8D4` (pink) |
| `agent:delivery-pm` | `#0EA5E9` (cyan) |
| `agent:engineer` | `#0E8A16` (green) |
| `agent:qa` | `#FBCA04` (yellow) |
| `agent:reviewer` | `#FB8C00` (orange) |

## How agents apply labels

- On issue creation: `gh issue create --label "type:X,state:Y,area:Z,agent:<self>"`
- On state transition: `gh issue edit <N> --add-label "state:new,agent:<self>" --remove-label "state:old"`
- Priority is set by PM (initial) or Delivery PM (during planning). Agents do not change priority unilaterally — they comment with a recommendation.
