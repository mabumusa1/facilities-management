# Platform Feature Flags

Feature flags allow platform super-admins to enable or disable platform-wide features for individual tenants. This gives you fine-grained control over what capabilities each tenant account can access.

## Accessing Feature Flags

1. Navigate to **Admin > Subscriptions**.
2. Click on a tenant account name to open the **Tenant Detail** page.
3. Select the **Features** tab.

Only super-admins (account owners) can access this tab. Tenant-level admins and other roles will be redirected to their dashboard.

## How Feature Flags Work

Each tenant has a subscription plan (e.g., Starter, Pro, Enterprise). Some features are **included by default** in a plan, and some are **not included**. Regardless of the plan default, a super-admin can manually enable or disable any feature for any tenant.

### Feature States

| State | Badge | Description |
|-------|-------|-------------|
| **Enabled** | Blue "Enabled" | Feature is active for this tenant |
| **Disabled** | Grey "Disabled" | Feature is inactive for this tenant |
| **In Plan** | ✓ Plan Name | Feature is included by default in the tenant's plan |
| **Not in Plan** | — Plan Name | Feature is not in the tenant's plan; manually overridden |

### Included vs. Not Included

- **Included in Plan:** The feature is part of the tenant's subscription tier by default. If no override exists, the feature is ON.
- **Not in Plan:** The feature is not part of the tenant's subscription tier. If no override exists, the feature is OFF. A super-admin can still enable it manually.

## Enabling a Feature

1. Locate the feature in the list.
2. Click the toggle switch to the ON position.
3. A confirmation dialog appears.
4. Click **Enable Feature** to confirm.

The feature becomes active immediately. Tenant users will gain access on their next page load.

## Disabling a Feature

1. Locate the feature in the list.
2. Click the toggle switch to the OFF position.
3. An alert dialog appears with an **Immediate Impact** warning describing what happens when the feature is disabled.
4. Click **Disable Feature** to confirm.

The feature becomes inactive immediately. Tenant users currently using the feature will see a "Feature not available" message on their next page load.

> **Note:** Disabling a feature is reversible. You can re-enable it at any time without data loss.

## Available Features

| Feature | Description | Included In |
|---------|-------------|-------------|
| Marketplace Module | Real estate marketplace for buying, selling, and renting properties | Starter, Pro, Enterprise |
| Power BI Connector | Integration with Microsoft Power BI for advanced reporting | Enterprise |
| Facilities Management | Booking and managing facility reservations (gym, pool, etc.) | Starter, Pro, Enterprise |
| Communication Hub | Announcements and notifications platform | Starter, Pro, Enterprise |
| Document Vault | Secure document storage and signing | Pro, Enterprise |
| Reports & Analytics | System-wide reporting and analytics dashboard | Starter, Pro, Enterprise |

## Audit Log

Every feature toggle is recorded in the audit log with:
- **Actor:** The super-admin who made the change
- **Timestamp:** When the change was made
- **Action:** Enabled or Disabled

Audit logs are accessible via the platform audit trail (future release).

## Troubleshooting

### A feature is missing from the list
Feature flags are defined in the platform code and seeded automatically. If a feature is missing, contact your engineering team.

### Toggle failed with an error
If a toggle fails (e.g., network issue), the switch returns to its previous state and an inline error message appears. Try again, or check your connection.
