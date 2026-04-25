# Parallel Implementation Plan

2-instance strategy: opencode + Claude Code working simultaneously on 170 stories.
Base: `1.x`. Worktrees: `.claude/worktrees/opencode/` and `.claude/worktrees/claude-code/`.

## Instance A — opencode (~87 stories)

### Phase 0 — Infrastructure (sequential, blocks all tracks)
- [ ] #243 — Auth Fortify gap audit
- [ ] #199 — Documents data model
- [ ] #224 — Settings ownership matrix

### Phase 1 — Auth (9 stories)
- [ ] #236 — Password reset
- [ ] #237 — Email verification (parallel with #236)
- [ ] #238 — Two-factor authentication (parallel with #236)
- [ ] #239 — Session management
- [ ] #240 — Profile self-service (parallel with #239)
- [ ] #241 — Password confirmation (parallel with #239)
- [ ] #244 — Logout
- [ ] #245 — 2FA recovery codes
- [ ] #242 — Admin user management

### Phase 1 — Documents (9 stories)
- [ ] #200 — Create & version document templates
- [ ] #201 — Preview generated document
- [ ] #202 — Generate document from template
- [ ] #203 — E-signature flow
- [ ] #204 — Download generated/signed docs
- [ ] #205 — ExcelSheet import template (parallel with #206)
- [ ] #206 — Bulk data export to Excel (parallel with #205)
- [ ] #207 — Document template format decision gate
- [ ] #208 — Historical version pinning

### Phase 2-3 — Settings (8 stories)
- [ ] #226 — Contract types (parallel #227..#231)
- [ ] #227 — InvoiceSetting (parallel)
- [ ] #228 — Regional settings (parallel)
- [ ] #229 — Form templates (parallel)
- [ ] #230 — ServiceSetting (parallel)
- [ ] #231 — Notification preferences (parallel)
- [ ] #232 — App appearance
- [ ] #233 — Settings audit trail

### Phase 2-3 — Communication (12 stories)
- [ ] #279 — Data model: gaps + Complaint + Suggestion + DirectoryEntry
- [ ] #280 — Announcement scheduling + targeting
- [ ] #281 — Read-receipt tracking (parallel with #282)
- [ ] #282 — Resident announcements feed (parallel with #281)
- [ ] #283 — Resident submits complaint
- [ ] #284 — Admin triages complaint queue
- [ ] #285 — Complaint→ServiceRequest conversion
- [ ] #286 — Complaint analytics
- [ ] #287 — Resident suggestion + upvote (parallel with #288)
- [ ] #288 — Admin responds to suggestions (parallel with #287)
- [ ] #289 — Admin creates community directory
- [ ] #290 — Resident browses directory

### Phase 2-3 — Contacts (9 stories)
- [ ] #149 — KYC documents on Resident (parallel #150..#152)
- [ ] #150 — Unit Owner contacts (parallel)
- [ ] #151 — Dependent contacts (parallel)
- [ ] #152 — Professional contacts (parallel)
- [ ] #153 — Bulk import contacts from Excel
- [ ] #154 — 360° activity history
- [ ] #155 — Archive/merge/reactivate
- [ ] #156 — Contacts list with search/filter/export
- [ ] #157 — Migrate existing residents/owners to unified Contacts

### Phase 2-3 — Properties (10 stories)
- [ ] #158 — Unit lifecycle state machine
- [ ] #159 — Unit status history
- [ ] #166 — Building metadata management
- [ ] #168 — Unit detail page
- [ ] #160 — Bulk import units (parallel with #161)
- [ ] #161 — Import error review + re-attempt (parallel with #160)
- [ ] #162 — Photo management
- [ ] #163 — Global unit search
- [ ] #164 — Export filtered units to Excel
- [ ] #167 — Bulk status change

### Phase 2-3 — Admin (13 stories)
- [ ] #291 — Data model: AccountSubscription, Lead, etc.
- [ ] #292 — Subscription overview (parallel #293, #294)
- [ ] #293 — Plan change request (parallel)
- [ ] #294 — Billing history (parallel)
- [ ] #295 — Seat management
- [ ] #296 — Leads list
- [ ] #297 — Lead detail
- [ ] #298 — Lead import
- [ ] #299 — Lead→Contact conversion
- [ ] #300 — Owner registration queue
- [ ] #301 — Feature flags
- [ ] #302 — Operations dashboard
- [ ] #131 — RBAC audit log

### Phase 2-3 — Marketplace (14 stories)
- [ ] #265 — Data model: MarketplaceUnit, Offer, Visit
- [ ] #266 — Listing eligibility
- [ ] #267 — Seller creates listing
- [ ] #268 — Seller manages listings
- [ ] #269 — Buyer browses listings
- [ ] #270 — Buyer submits inquiry
- [ ] #271 — Seller manages inquiry queue
- [ ] #272 — Buyer requests visit
- [ ] #273 — Seller records visit outcome
- [ ] #274 — Buyer submits offer
- [ ] #275 — Seller accepts/counters/rejects
- [ ] #276 — Offer→Lease quote trigger
- [ ] #277 — Featured listings + promotions
- [ ] #278 — Conversion funnel analytics

---

## Instance B — Claude Code (~84 stories)

### Phase 1 — Leasing + Accounting (30 stories)
- Leasing: #169→#170→#171→#172→#173→#175→#176→#177∥#178∥#179→#180→#181→#182→#183→#184
  (15 stories)
- Accounting: #185→#186∥#187→#188→#189→#190→#191→#192→#193→#194→#195→#196→#197→#198
  (14 stories)
- Note: #175+#188 must be co-designed; #176 blocked by Documents #202/#203

### Phase 1 — Service Requests (15 stories)
- #222→#209→#210→#211→#212→#213→#214→#215→#220→#216→#217→#218→#219→#221→#223

### Phase 1 — Facilities (11 stories)
- #246→#247→#248→#249→#250→#251→#252→#253→#254→#255→#256
- Note: #253 blocked by Documents #202/#203; #254 blocked by Accounting #190

### Phase 1 — Visitor Access (8 stories)
- #257→#258→#259→#260→#261→#262→#263→#264

### Phase 2-3 — Reports + PowerBI (20 stories)
- Reports: #303→#304∥#305∥#306→#307→#308→#309→#310→#311→#312→#313 (11)
- PowerBI: #314→#315→#316→#317→#318→#319→#320→#321→#322 (9)

---

## Shared Protocol

### Claim a story
```bash
gh issue edit <N> --repo mabumusa1/facilities-management \
  --remove-label "state:ready-for-impl" \
  --add-label "state:in-progress,agent:engineer" \
  --assignee @me
```

### Create worktree + branch
```bash
# opencode
git worktree add -b <area>/<slug>-#<N> .claude/worktrees/opencode

# Claude Code
git worktree add -b <area>/<slug>-#<N> .claude/worktrees/claude-code
```

### Pre-commit checklist
```bash
php artisan wayfinder:generate
vendor/bin/pint --dirty --format agent
php artisan test --compact tests/Feature/<StoryTest>.php
```

### Open PR + transition state
```bash
gh pr create --repo mabumusa1/facilities-management --base 1.x \
  --title "feat: <area>: <title>" --body "Closes #<N>"
gh issue edit <N> --add-label "state:in-review" --remove-label "state:in-progress"
```

### After PR merge — cleanup worktree
```bash
git worktree remove .claude/worktrees/<instance> --force
```

---

## Phase Gates

| Gate | Trigger | Action |
|------|---------|--------|
| Phase 0→1 | #243, #199, #224 merged | Both instances launch Phase 1 tracks |
| B: #169 merged | Leasing data model done | Start accounting #185 in parallel |
| B: #175 merged | Lease activation done | Start accounting #188 |
| B: #222 merged | SR data model done | Start SR features #209→ |
| A: #200 merged | Document templates done | Start document generation #201→ |
| Phase 1→2 | All Phase 1 complete | Launch Phase 2-3 tracks |

## Conflict Guarantees

- Zero file overlap between Instance A and Instance B tracks
- opencode areas: auth, documents, communication, contacts, properties, settings, admin, marketplace
- Claude Code areas: leasing, accounting, service-requests, facilities, visitor-access, reports
- Migration tables are area-segregated
- Routes are area-prefixed
- Auth (User) and Settings shared models: opencode modifies them first; Claude Code reads only
