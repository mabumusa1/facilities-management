# Project Views — Manual Setup

The `gh` CLI does not expose Projects v2 view creation cleanly. After running `setup.sh`, create these four saved views in the GitHub UI.

## Prerequisite

`setup.sh` has run, the `Product Council` project exists, and all fields are configured.

Open the project: GitHub → your profile → Projects → `Product Council`.

## View 1 — By Status (Board layout)

- Click **+ New view** → **Board**.
- Rename to `By Status`.
- **Group by:** `Status` field.
- **Sort by:** `Priority` (ascending — p0 first), then `Updated` (descending).
- **Filter:** `is:open` (hides closed/done items by default).
- Save.

This is the default kanban view — drag cards between columns to mirror state-label transitions.

## View 2 — By Area (Table layout)

- **+ New view** → **Table**.
- Rename to `By Area`.
- **Group by:** `Area`.
- **Sort by:** `Priority` ascending.
- **Visible columns:** Title, Type, Status, Priority, Agent, Sprint, Assignees.
- **Filter:** none (show all).
- Save.

Use to see workload distribution across app domains.

## View 3 — By Sprint (Table layout)

- **+ New view** → **Table**.
- Rename to `By Sprint`.
- **Group by:** `Sprint` (iteration field).
- **Sort by:** `Status`, then `Priority` ascending.
- **Visible columns:** Title, Type, Status, Priority, Area, Agent.
- **Filter:** `Sprint` is current or future.
- Save.

Use during sprint planning and standups.

## View 4 — By Agent (Table layout)

- **+ New view** → **Table**.
- Rename to `By Agent`.
- **Group by:** `Agent`.
- **Sort by:** `Status`, then `Priority` ascending.
- **Visible columns:** Title, Type, Status, Priority, Area, Sprint.
- **Filter:** `is:open`.
- Save.

Use to spot overloaded or idle agents — the Delivery PM agent's `/dpm-status` report uses the same lens.

## Optional view 5 — Blocked

- **+ New view** → **Table**.
- Rename to `Blocked`.
- **Filter:** `Status = Blocked`.
- **Sort by:** `Priority`, `Updated` descending.
- Save.

Use to quickly surface anything stuck.
