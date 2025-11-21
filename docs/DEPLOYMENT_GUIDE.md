# Production Deployment Guide - Role & Permission System

## Understanding the `role_user` Pivot Table

The `role_user` pivot table stores the relationship between users and roles. It contains:
- `user_id` - The ID of the user
- `role_id` - The ID of the role
- `created_at` - Timestamp when the role was assigned
- `updated_at` - Timestamp when the record was last updated

---

## What Happens When Seeders Run

### Before (Old Behavior):
❌ **No data was stored in `role_user` table when seeders ran**
- `PermissionSeeder` → Creates permissions
- `RoleSeeder` → Creates roles and assigns permissions to roles
- `UserSeeder` → Creates users but **DOES NOT assign roles**

**Result:** Users had no roles assigned, so they couldn't access anything!

---

### After (Updated Behavior):
✅ **Data IS stored in `role_user` table when seeders run**

1. **PermissionSeeder** → Creates all permissions
2. **RoleSeeder** → Creates roles (super-admin, administrator) and assigns permissions to them
3. **UserSeeder** → Creates users AND assigns Super Admin role to `harsukh21@gmail.com`

**Result:** Default admin user has Super Admin role and can access everything!

---

## Updated Seeder Files

### 1. `UserSeeder.php` - Now Assigns Roles
```php
// Create the default admin user
$user = User::updateOrCreate(
    ['email' => 'harsukh21@gmail.com'],
    [
        'name' => 'Harsukh',
        'email' => 'harsukh21@gmail.com',
        'password' => Hash::make('Har#$785'),
        'email_verified_at' => now(),
    ]
);

// Assign Super Admin role to the default user
$superAdminRole = Role::where('slug', 'super-admin')->first();
if ($superAdminRole) {
    $user->assignRoles([$superAdminRole->id]);
    // Clear and reload cache
    $user->clearPermissionCache();
    $user->loadPermissionsIntoCache();
    $user->loadRolesIntoCache();
}
```

### 2. `DatabaseSeeder.php` - Correct Execution Order
```php
$this->call([
    PermissionSeeder::class,  // Must run first - creates permissions
    RoleSeeder::class,        // Must run second - creates roles and assigns permissions
    UserSeeder::class,        // Must run third - creates users and assigns roles
    EventSeeder::class,
    MarketListSeeder::class,
]);
```

---

## Production Deployment Steps

### Step 1: Run Migrations
```bash
php artisan migrate
```

This creates all necessary tables including:
- `permissions` table
- `roles` table
- `role_permission` pivot table
- `role_user` pivot table (THIS IS WHERE USER-ROLE RELATIONSHIPS ARE STORED)
- `users` table

### Step 2: Run Seeders (In Correct Order)

**Option A: Run all seeders (Recommended)**
```bash
php artisan db:seed
```

This will run seeders in the correct order:
1. PermissionSeeder → Creates permissions
2. RoleSeeder → Creates roles and assigns permissions
3. UserSeeder → Creates admin user and assigns Super Admin role
4. Other seeders...

**Option B: Run seeders individually (If needed)**
```bash
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
```

### Step 3: Verify Data in `role_user` Table

Check that roles are assigned to users:

```bash
php artisan tinker
```

```php
// Check role_user table
DB::table('role_user')->get();

// Check user roles
$user = User::where('email', 'harsukh21@gmail.com')->first();
$user->roles; // Should show Super Administrator role

// Check user permissions
$user->getCachedPermissions(); // Should show all permissions
```

### Step 4: Test Login

Login with the default admin user:
- **Email:** `harsukh21@gmail.com`
- **Password:** `Har#$785`

You should have access to everything as Super Admin.

---

## How Roles Are Assigned

### Using `assignRoles()` Method

The `assignRoles()` method in the `User` model uses Laravel's `sync()` method:

```php
public function assignRoles(array $roleIds)
{
    return $this->roles()->sync($roleIds);
}
```

**What `sync()` does:**
1. **Detaches** all existing roles for the user
2. **Attaches** the new roles specified in the array
3. **Stores** the relationship in the `role_user` pivot table

**Example:**
```php
$user->assignRoles([1, 2]); // Assigns role IDs 1 and 2 to the user
```

This will create records in `role_user` table:
```
user_id | role_id | created_at | updated_at
--------|---------|------------|------------
1       | 1       | 2025-01-XX | 2025-01-XX
1       | 2       | 2025-01-XX | 2025-01-XX
```

---

## Assigning Roles to Existing Users

If you already have users in production and want to assign roles:

### Method 1: Using Tinker
```bash
php artisan tinker
```

```php
// Get user
$user = User::where('email', 'user@example.com')->first();

// Get role
$role = Role::where('slug', 'administrator')->first();

// Assign role
$user->assignRoles([$role->id]);

// Clear and reload cache
$user->clearPermissionCache();
$user->loadPermissionsIntoCache();
$user->loadRolesIntoCache();
```

### Method 2: Using Artisan Command (Recommended for Production)
```bash
php artisan tinker
```

```php
// Assign Super Admin to existing user
$user = User::find(USER_ID);
$superAdmin = Role::where('slug', 'super-admin')->first();
$user->assignRoles([$superAdmin->id]);
$user->clearPermissionCache();
$user->loadPermissionsIntoCache();
$user->loadRolesIntoCache();
```

### Method 3: Create a Deployment Script

Create `database/seeders/AssignRolesToExistingUsersSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AssignRolesToExistingUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Get roles
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $admin = Role::where('slug', 'administrator')->first();

        // Assign Super Admin to specific users
        $superAdminUsers = ['harsukh21@gmail.com'];
        foreach ($superAdminUsers as $email) {
            $user = User::where('email', $email)->first();
            if ($user && $superAdmin) {
                $user->assignRoles([$superAdmin->id]);
                $user->clearPermissionCache();
                $user->loadPermissionsIntoCache();
                $user->loadRolesIntoCache();
                $this->command->info("✅ Assigned Super Admin to {$email}");
            }
        }

        // Assign Admin to other users
        $adminUsers = ['admin@example.com'];
        foreach ($adminUsers as $email) {
            $user = User::where('email', $email)->first();
            if ($user && $admin) {
                $user->assignRoles([$admin->id]);
                $user->clearPermissionCache();
                $user->loadPermissionsIntoCache();
                $user->loadRolesIntoCache();
                $this->command->info("✅ Assigned Administrator to {$email}");
            }
        }
    }
}
```

Then run:
```bash
php artisan db:seed --class=AssignRolesToExistingUsersSeeder
```

---

## Verifying Deployment

### 1. Check Database Tables
```sql
-- Check roles exist
SELECT * FROM roles;

-- Check permissions exist
SELECT * FROM permissions;

-- Check role_permission relationships
SELECT * FROM role_permission;

-- Check role_user relationships (IMPORTANT!)
SELECT * FROM role_user;

-- Check users have roles
SELECT u.id, u.name, u.email, r.name as role_name
FROM users u
LEFT JOIN role_user ru ON u.id = ru.user_id
LEFT JOIN roles r ON ru.role_id = r.id;
```

### 2. Test Permission System
```bash
php artisan permissions:test
```

This will show all users and their roles.

### 3. Test Login and Access
- Login with admin user
- Try accessing protected routes
- Verify menu items show/hide based on permissions

---

## Troubleshooting

### Problem: Users have no roles after seeding

**Solution:**
1. Check if RoleSeeder ran successfully: `SELECT * FROM roles;`
2. Check if UserSeeder assigned roles: `SELECT * FROM role_user;`
3. Manually assign roles using tinker (see above)

### Problem: Users can't access anything

**Cause:** Users don't have roles assigned

**Solution:**
```bash
php artisan tinker
```

```php
$user = User::where('email', 'your-email@example.com')->first();
$role = Role::where('slug', 'super-admin')->first();
$user->assignRoles([$role->id]);
$user->clearPermissionCache();
$user->loadPermissionsIntoCache();
$user->loadRolesIntoCache();
```

### Problem: Cache not refreshing

**Solution:**
```bash
php artisan cache:clear
php artisan tinker
```

```php
$user = User::find(USER_ID);
$user->clearPermissionCache();
$user->loadPermissionsIntoCache();
$user->loadRolesIntoCache();
```

---

## Summary

✅ **Updated Seeders:** UserSeeder now assigns Super Admin role to default user

✅ **DatabaseSeeder:** Updated to run seeders in correct order

✅ **role_user Table:** Will be populated when seeders run

✅ **Default Admin:** `harsukh21@gmail.com` will have Super Admin role after seeding

---

## Production Checklist

Before deploying to production:

- [ ] Run migrations: `php artisan migrate`
- [ ] Run seeders: `php artisan db:seed`
- [ ] Verify `role_user` table has data: `SELECT * FROM role_user;`
- [ ] Verify admin user has Super Admin role
- [ ] Test login with admin user
- [ ] Verify permissions are working
- [ ] Assign roles to any existing users
- [ ] Clear cache: `php artisan cache:clear`

After deployment:

- [ ] Login with admin user
- [ ] Check menu items show correctly
- [ ] Test accessing protected routes
- [ ] Verify permission checks work
- [ ] Assign roles to other users as needed

