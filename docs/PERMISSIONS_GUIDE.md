# Permissions Guide

This document explains how permissions are structured in the application and how to create/manage them.

## Permission Structure

Permissions are organized by functional groups that match the application's module structure:

### 1. Dashboard Group
- **View Dashboard**: Access to view the dashboard overview

### 2. Users Management Group
- **View Users**: View list of users
- **Create Users**: Create new users
- **Edit Users**: Edit existing users
- **Delete Users**: Delete users
- **Manage User Roles**: Assign or remove roles from users
- **Update User Status**: Activate or deactivate users

### 3. Roles & Permissions Group
- **View Roles**: View list of roles and their permissions
- **Create Roles**: Create new roles
- **Edit Roles**: Edit existing roles and permissions
- **Delete Roles**: Delete roles
- **Manage Permissions**: Create and manage permissions

### 4. Events Management Group
- **View Events**: View list of events
- **View All Events**: Access to all events listing page
- **View Event Details**: View detailed information about events
- **Edit Events**: Edit event information
- **Update Event Market Time**: Update market time for events
- **Bulk Update Events**: Perform bulk updates on events
- **Export Events**: Export events to CSV

### 5. Markets Management Group
- **View Markets**: View list of markets
- **View All Markets**: Access to all markets listing page
- **View Market Details**: View detailed information about markets
- **Export Markets**: Export markets to CSV

### 6. Market Rates Group
- **View Market Rates**: View market rates listing
- **View Market Rate Details**: View detailed market rate information
- **Export Market Rates**: Export market rates to CSV

### 7. Risk Management Group
- **View Risk Markets**: View pending and completed risk markets
- **Manage Risk Labels**: Update labels on risk markets (4x, b2c, b2b, USDT)
- **Mark Risk as Done**: Mark risk markets as completed with remarks

### 8. System Logs Group
- **View System Logs**: View application system logs
- **Download System Logs**: Download log files
- **Clear System Logs**: Clear log files

### 9. Performance Monitoring Group
- **View Performance Metrics**: View system performance metrics
- **Refresh Performance Data**: Refresh performance monitoring data

### 10. Settings Group
- **View Settings**: Access general settings
- **Manage General Settings**: Update general application settings
- **Clear Cache**: Clear application cache
- **Optimize Application**: Run application optimization commands

### 11. Profile Management Group
- **View Own Profile**: View own user profile
- **Edit Own Profile**: Update own profile information
- **Change Own Password**: Change own account password
- **Manage Two-Factor Authentication**: Enable or disable 2FA for own account
- **Manage Own Sessions**: View and terminate own active sessions

## Default Roles

The application comes with the following default roles:

### 1. Super Administrator
- **Slug**: `super-admin`
- **Description**: Full access to all features and settings
- **Permissions**: All permissions

### 2. Administrator
- **Slug**: `administrator`
- **Description**: Administrative access with most permissions
- **Permissions**: Most permissions except sensitive operations (delete users, delete roles, clear logs, optimize)

### 3. Manager
- **Slug**: `manager`
- **Description**: Can manage events, markets, and view reports
- **Permissions**: Dashboard, Events, Markets, Market Rates, Risk, and Profile permissions

### 4. Editor
- **Slug**: `editor`
- **Description**: Can view and edit events and markets
- **Permissions**: View and edit events/markets, view market rates, manage own profile

### 5. Viewer
- **Slug**: `viewer`
- **Description**: Read-only access to view data
- **Permissions**: View-only permissions for events, markets, and market rates

## Creating Permissions

### Via Seeder (Recommended)

1. Edit `database/seeders/PermissionSeeder.php`
2. Add your permission to the appropriate group:
```php
'Your Group' => [
    [
        'name' => 'Your Permission Name',
        'description' => 'Description of what this permission allows',
    ],
],
```
3. Run the seeder:
```bash
php artisan db:seed --class=PermissionSeeder
```

### Via Code (Programmatically)

```php
use App\Models\Permission;
use Illuminate\Support\Str;

$permission = Permission::create([
    'name' => 'Your Permission Name',
    'slug' => Str::slug('Your Permission Name'),
    'description' => 'Description of what this permission allows',
    'group' => 'Your Group Name',
]);
```

### Via Database

You can also insert directly into the database:

```sql
INSERT INTO permissions (name, slug, description, group, created_at, updated_at)
VALUES ('Your Permission Name', 'your-permission-name', 'Description', 'Your Group', NOW(), NOW());
```

## Assigning Permissions to Roles

### Via Admin Interface

1. Navigate to **Users** → **Roles List**
2. Click **Create Role** or edit an existing role
3. Select the permissions you want to assign
4. Save the role

### Via Code

```php
use App\Models\Role;
use App\Models\Permission;

$role = Role::find(1); // or use where('slug', 'manager')->first()

// Sync all permissions (replaces existing)
$role->permissions()->sync([1, 2, 3, 4]); // Permission IDs

// Add specific permissions (keeps existing)
$role->permissions()->attach([5, 6]); // Permission IDs

// Remove specific permissions
$role->permissions()->detach([1, 2]); // Permission IDs
```

## Assigning Roles to Users

### Via Admin Interface

1. Navigate to **Users** → **User List**
2. Click on a user to view details
3. Click the edit icon in the "Roles & Permissions" section
4. Select the roles you want to assign
5. Save

### Via Code

```php
use App\Models\User;
use App\Models\Role;

$user = User::find(1);
$role = Role::where('slug', 'manager')->first();

// Assign role
$user->roles()->attach($role->id);

// Or sync (replaces all existing roles)
$user->roles()->sync([$role->id]);
```

## Checking Permissions in Code

```php
// Check if user has a specific role
if ($user->hasRole('manager')) {
    // User has manager role
}

// Check if user has a specific permission (via roles)
if ($user->hasPermission('edit-events')) {
    // User has edit-events permission through their roles
}

// Check role directly
if ($role->permissions()->where('slug', 'edit-events')->exists()) {
    // Role has edit-events permission
}
```

## Best Practices

1. **Group Related Permissions**: Organize permissions by functional groups (Users, Events, Markets, etc.)
2. **Use Descriptive Names**: Permission names should clearly describe what they allow
3. **Follow Naming Conventions**: Use kebab-case for slugs (e.g., `view-users`, `edit-events`)
4. **Principle of Least Privilege**: Assign only necessary permissions to roles
5. **Regular Review**: Periodically review and update permissions as the application evolves

## Re-seeding Permissions

If you need to re-seed permissions (useful after adding new permissions to the seeder):

```bash
# This will update existing permissions and create new ones
php artisan db:seed --class=PermissionSeeder
```

Note: The seeder uses `updateOrCreate`, so it won't create duplicates and will update existing permissions if their data has changed.

