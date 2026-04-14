# System Admin User Setup Guide

## Overview

This guide explains how to set up and login with the system admin user for the Facilities Management System. The admin user has full system-wide access and can manage all properties, users, and settings.

## What Changed

We've created a clean, maintainable system for managing the first admin user:

1. **New AdminUserSeeder** - Dedicated seeder file for creating the system admin
2. **Updated UserFactory** - Enhanced with convenient states for creating different user types
3. **New AdminAuthenticationTest** - Comprehensive tests for admin authentication
4. **Updated DatabaseSeeder** - Simplified to use the new AdminUserSeeder

## Default Admin Credentials

Use these credentials to login for the first time:

- **Email**: `admin@example.com`
- **Password**: `password`

**Important**: Change these credentials after your first login for security reasons.

## Setup Steps

### 1. Ensure Database is Running

First, make sure your database service is running. If using Docker/Sail:

```bash
./vendor/bin/sail up -d
```

### 2. Run Migrations

Apply all database migrations:

```bash
php artisan migrate
```

Or with Sail:

```bash
./vendor/bin/sail artisan migrate
```

### 3. Seed the Database

Seed the database with reference data and create the admin user:

```bash
php artisan db:seed
```

Or with Sail:

```bash
./vendor/bin/sail artisan db:seed
```

This command will:
- Create all roles and permissions
- Create all reference data (countries, currencies, cities, etc.)
- Create the system admin user with email `admin@example.com`

### 4. Login

1. Navigate to your application URL (typically `http://localhost`)
2. Click "Log in" or go to `/login`
3. Enter credentials:
   - Email: `admin@example.com`
   - Password: `password`
4. Click "Log in"
5. You'll be redirected to the dashboard

## Admin User Properties

The system admin user has these properties:

| Property | Value | Purpose |
|----------|-------|---------|
| `contact_type` | Admin | Identifies user role type |
| `manager_role` | Admin (1) | Manager role within the system |
| `is_all_communities` | true | Can access all communities |
| `is_all_buildings` | true | Can access all buildings |
| `email_verified_at` | now() | Email pre-verified |
| `two_factor_confirmed_at` | null | 2FA not enabled by default |

## Creating Additional Admins

You can create additional admin users using the factory in tests or seeders:

```php
// Using the factory with admin state
$admin = User::factory()->admin()->create([
    'name' => 'Another Admin',
    'email' => 'another-admin@example.com',
    'password' => 'newpassword',
]);

// Assign the Admins role
$admin->assignRole('Admins');
```

Or manually creating and assigning the role:

```php
$user = User::create([
    'name' => 'Admin User',
    'email' => 'admin@company.com',
    'password' => 'password', // Will be hashed automatically
    'contact_type' => ContactType::Admin,
    'manager_role' => ManagerRole::Admin,
    'is_all_communities' => true,
    'is_all_buildings' => true,
]);

$user->assignRole('Admins');
```

## Admin Middleware & Authorization

The application uses several layers of authentication:

1. **`auth` middleware** - User must be authenticated
2. **`verified` middleware** - User email must be verified (pre-verified for admin)
3. **`verified.user` middleware** - Custom check for professional users with manager roles

The admin user passes all these checks by default.

## Admin Roles & Permissions

The admin role has full system permissions defined in `app/Services/PermissionGenerator.php`. These are synced during seeding.

After login, the admin can:
- View and manage all communities
- View and manage all buildings and units
- View and manage all leases
- View and manage all contacts
- View and manage all service requests
- Generate reports
- Configure system settings

## Testing Admin Setup

We've included comprehensive tests in `tests/Feature/AdminAuthenticationTest.php`:

```bash
# Run all admin authentication tests
php artisan test tests/Feature/AdminAuthenticationTest.php --compact

# Run a specific test
php artisan test tests/Feature/AdminAuthenticationTest.php --filter=test_admin_can_login_with_credentials --compact
```

Test coverage includes:
- ✅ Admin user creation via seeder
- ✅ Admin login with valid credentials
- ✅ Login failure with wrong password
- ✅ Dashboard access for authenticated admin
- ✅ Admin factory state functionality

## Troubleshooting

### "Unable to locate file in Vite manifest" Error

If you see this error, rebuild the frontend assets:

```bash
npm run build
# or for development
npm run dev
```

### Database Connection Error

Make sure your database is running. If using Sail:

```bash
./vendor/bin/sail up -d
```

### Email Not Verifying After Login

The admin user has `email_verified_at` set to `now()` by default, so email verification should not be required. If you're being redirected to verify email, check that the middleware in `routes/web.php` allows admin users to skip this step.

### Password Not Hashing

The User model has `password` cast to `'hashed'`, which automatically hashes the password before storing it. Make sure you're passing a plain text password (it will be hashed automatically).

## Advanced: Custom Admin Setup

To customize the admin user created by the seeder, edit `database/seeders/AdminUserSeeder.php`:

```php
$admin = User::firstOrCreate(
    ['email' => 'admin@example.com'],
    [
        'name' => 'Custom Admin Name',
        'password' => 'custom_password',
        'email_verified_at' => now(),
        'contact_type' => ContactType::Admin,
        'manager_role' => ManagerRole::Admin,
        'is_all_communities' => true,
        'is_all_buildings' => true,
    ]
);
```

Then re-run: `php artisan db:seed --class=AdminUserSeeder`

## References

- **User Model**: `app/Models/User.php`
- **Admin Seeder**: `database/seeders/AdminUserSeeder.php`
- **Database Seeder**: `database/seeders/DatabaseSeeder.php`
- **User Factory**: `database/factories/UserFactory.php`
- **Auth Tests**: `tests/Feature/AdminAuthenticationTest.php`
- **Fortify Config**: `config/fortify.php`
- **Routes**: `routes/web.php`

## Security Notes

1. **Change Default Password**: After first login, immediately change the default password
2. **Use Strong Passwords**: Admin accounts should use strong, unique passwords
3. **Enable 2FA**: Consider enabling two-factor authentication for the admin account
4. **Rotate Credentials**: Regularly rotate admin credentials
5. **Audit Logs**: Monitor admin activities through audit logs

---

For questions or issues, refer to the Laravel and Fortify documentation or check the test cases for usage examples.
