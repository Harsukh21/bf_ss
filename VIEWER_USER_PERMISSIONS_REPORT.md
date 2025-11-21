# Viewer User Permissions Report

## User Information
- **Name:** Viewer
- **Email:** viewer@gmail.com
- **User ID:** 2

## Assigned Roles
- **Role:** Viewer (slug: `viewer`)

## Current Permissions (18 total)

The `viewer@gmail.com` user has the following permissions assigned via the "Viewer" role:

### View Permissions
1. `view-dashboard`
2. `view-events`
3. `view-all-events`
4. `view-event-details`
5. `view-markets`
6. `view-all-markets`
7. `view-market-details`
8. `view-market-rates`
9. `view-market-rate-details`
10. `view-risk-markets`
11. `view-scorecard`
12. `view-event-markets`
13. `view-notifications`
14. `view-notification-details`

### Profile Permissions
15. `view-own-profile`
16. `edit-own-profile`
17. `change-own-password`

### Notification Permissions
18. `mark-notifications-as-read`

## Sidebar Menu Visibility Status

Based on the permissions above, here's what should be visible in the sidebar:

| Menu Item | Permission Required | Has Permission | Status |
|-----------|---------------------|----------------|--------|
| **Dashboard** | None (always visible) | N/A | ✅ **Visible** |
| **Users** | `view-users` OR `view-roles` | ❌ No | ❌ **Hidden** |
| **Events** | `view-events` | ✅ Yes | ✅ **Visible** |
| **Markets** | `view-markets` | ✅ Yes | ✅ **Visible** |
| **SS (Market Rates)** | `view-market-rates` | ✅ Yes | ✅ **Visible** |
| **Scorecard** | `view-scorecard` | ✅ Yes | ✅ **Visible** |
| **Risk** | `view-risk-markets` | ✅ Yes | ✅ **Visible** |
| **Notifications** | `view-notifications` | ✅ Yes | ✅ **Visible** |
| **Testing** | `access-testing-module` | ❌ No | ❌ **Hidden** |
| **Settings** | `manage-general-settings` OR `view-system-logs` OR `view-performance-metrics` OR `view-settings` | ❌ No | ❌ **Hidden** |

## Summary

**Current Behavior:** The sidebar is working **correctly** based on the permissions assigned to the "Viewer" role.

The user can see:
- ✅ Dashboard
- ✅ Events (and submenu items)
- ✅ Markets (and submenu items)
- ✅ SS (Market Rates)
- ✅ Scorecard
- ✅ Risk
- ✅ Notifications

The user cannot see:
- ❌ Users
- ❌ Testing
- ❌ Settings

## Issue Analysis

If you're seeing all menu items (including Users, Testing, Settings), this could mean:

1. **Cache Issue:** The user's permission cache might be stale. Try:
   ```bash
   php artisan tinker
   >>> $user = App\Models\User::where('email', 'viewer@gmail.com')->first();
   >>> $user->clearPermissionCache();
   >>> $user->loadPermissionsIntoCache();
   >>> $user->loadRolesIntoCache();
   ```

2. **Super Admin Role:** Check if the user accidentally has the `super-admin` role (which grants all permissions):
   ```bash
   php artisan tinker
   >>> $user = App\Models\User::where('email', 'viewer@gmail.com')->first();
   >>> $user->hasRole('super-admin'); // Should return false
   ```

3. **Permission Check Logic:** The `hasPermission()` method in the User model grants all permissions if the user has the `super-admin` role. Verify the user doesn't have this role.

## Recommendations

If you want to limit the "Viewer" role to have **read-only access** with fewer permissions, you should:

1. **Update the Viewer Role Permissions:**
   - Remove permissions like `view-all-events`, `view-all-markets`
   - Keep only basic view permissions
   - Remove `view-risk-markets` if viewers shouldn't access risk module
   - Remove `view-scorecard` if viewers shouldn't access scorecard

2. **Clear and Reload User Cache:**
   After updating permissions, clear the user's cache:
   ```bash
   php artisan tinker
   >>> $user = App\Models\User::where('email', 'viewer@gmail.com')->first();
   >>> $user->clearPermissionCache();
   >>> $user->loadPermissionsIntoCache();
   >>> $user->loadRolesIntoCache();
   ```

3. **Logout and Login Again:**
   The user needs to logout and login again for the changes to take effect (cache is loaded on login).

## How to Check Current State

Run this command to see the current permissions and sidebar visibility:

```bash
php artisan tinker
```

Then execute:
```php
$user = App\Models\User::where('email', 'viewer@gmail.com')->first();
echo "Roles: " . implode(', ', $user->roles->pluck('slug')->toArray()) . PHP_EOL;
echo "Permissions: " . count($user->getCachedPermissions()) . PHP_EOL;
echo "Has super-admin role: " . ($user->hasRole('super-admin') ? 'Yes' : 'No') . PHP_EOL;
echo PHP_EOL;
echo "Sidebar Visibility:" . PHP_EOL;
echo "  Dashboard: " . ($user->id ? 'Yes' : 'No') . PHP_EOL;
echo "  Users: " . (($user->hasPermission('view-users') || $user->hasPermission('view-roles')) ? 'Yes' : 'No') . PHP_EOL;
echo "  Events: " . ($user->hasPermission('view-events') ? 'Yes' : 'No') . PHP_EOL;
echo "  Markets: " . ($user->hasPermission('view-markets') ? 'Yes' : 'No') . PHP_EOL;
echo "  SS: " . ($user->hasPermission('view-market-rates') ? 'Yes' : 'No') . PHP_EOL;
echo "  Scorecard: " . ($user->hasPermission('view-scorecard') ? 'Yes' : 'No') . PHP_EOL;
echo "  Risk: " . ($user->hasPermission('view-risk-markets') ? 'Yes' : 'No') . PHP_EOL;
echo "  Notifications: " . ($user->hasPermission('view-notifications') ? 'Yes' : 'No') . PHP_EOL;
echo "  Testing: " . ($user->hasPermission('access-testing-module') ? 'Yes' : 'No') . PHP_EOL;
echo "  Settings: " . (($user->hasPermission('manage-general-settings') || $user->hasPermission('view-system-logs') || $user->hasPermission('view-performance-metrics') || $user->hasPermission('view-settings')) ? 'Yes' : 'No') . PHP_EOL;
```

