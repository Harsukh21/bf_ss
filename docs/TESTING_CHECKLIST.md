# Role & Permission Testing Checklist

## Phase 8: Testing Guide

This document provides a comprehensive testing checklist to verify that the role-permission system is working correctly.

---

## Prerequisites

Before testing, ensure:
- [ ] All migrations are run: `php artisan migrate`
- [ ] Seeders are run: `php artisan db:seed --class=PermissionSeeder && php artisan db:seed --class=RoleSeeder`
- [ ] You have users with different roles assigned
- [ ] Cache is cleared: `php artisan cache:clear`

---

## Test 1: Login & Cache Verification

### Steps:
1. **Login as a user with roles assigned**
   - [ ] Login with password or Web PIN
   - [ ] Should redirect to dashboard successfully

2. **Verify Cache is Created**
   ```bash
   php artisan tinker
   ```
   ```php
   // Check if cache exists for user ID 1
   Cache::get('user_permissions_1');
   Cache::get('user_roles_1');
   
   // Should return arrays of permissions and roles
   ```

3. **Verify Cache Contains Correct Data**
   ```php
   $user = App\Models\User::find(1);
   $permissions = $user->getCachedPermissions();
   $roles = $user->getCachedRoles();
   
   // Print permissions and roles
   print_r($permissions);
   print_r($roles);
   ```

**Expected Result:**
- Cache keys exist: `user_permissions_{user_id}` and `user_roles_{user_id}`
- Permissions array contains permission slugs (e.g., `['view-notifications', 'create-notifications', ...]`)
- Roles array contains role slugs (e.g., `['manager', 'editor', ...]`)

---

## Test 2: Route Protection

### Test 2.1: Access Routes WITH Permission

**Steps:**
1. Login as a user with `view-notifications` permission
2. Try to access: `http://localhost:8000/notifications`
   - [ ] Should load the notifications page successfully
   - [ ] No 403 error

3. Login as a user with `create-notifications` permission
4. Try to access: `http://localhost:8000/notifications/create`
   - [ ] Should load the create notification page successfully
   - [ ] No 403 error

**Expected Result:** Routes load successfully if user has permission.

---

### Test 2.2: Access Routes WITHOUT Permission

**Steps:**
1. Login as a user WITHOUT `view-notifications` permission
2. Try to access: `http://localhost:8000/notifications`
   - [ ] Should get 403 Forbidden error
   - [ ] Error message: "You do not have permission to access this resource."

3. Login as a user WITHOUT `create-notifications` permission
4. Try to access: `http://localhost:8000/notifications/create`
   - [ ] Should get 403 Forbidden error

5. Try to access: `http://localhost:8000/testing/telegram`
   - [ ] Should get 403 if user doesn't have `access-testing-module` permission

**Expected Result:** 403 errors for routes without required permissions.

---

### Test 2.3: Test All Protected Routes

**Notifications:**
- [ ] `/notifications` - requires `view-notifications`
- [ ] `/notifications/create` - requires `create-notifications`
- [ ] `/notifications/{id}/edit` - requires `edit-notifications`
- [ ] DELETE `/notifications/{id}` - requires `delete-notifications`
- [ ] `/notifications/{id}` - requires `view-notification-details`
- [ ] `/notifications/pending` - requires `view-pending-notifications`
- [ ] `/notifications/push/pending` - requires `manage-push-notifications`

**Testing:**
- [ ] `/testing/telegram` - requires `access-testing-module`
- [ ] POST `/testing/telegram/send` - requires `send-telegram-test-messages`

**Settings:**
- [ ] `/settings` - requires `view-settings`
- [ ] `/settings/create` - requires `create-settings`
- [ ] `/settings/{setting}/edit` - requires `edit-settings`
- [ ] DELETE `/settings/{setting}` - requires `delete-settings`

**Scorecard:**
- [ ] `/scorecard` - requires `view-scorecard`
- [ ] `/scorecard/events/{id}/markets` - requires `view-event-markets`
- [ ] POST `/scorecard/events/{id}/update` - requires `update-scorecard-events`
- [ ] POST `/scorecard/events/{id}/update-labels` - requires `update-scorecard-labels`

---

## Test 3: Blade View Display

### Test 3.1: Sidebar Menu Visibility

**Steps:**
1. Login as different users with different roles
2. Check sidebar menu items

**Notifications Menu:**
- [ ] Visible if user has `view-notifications` permission
- [ ] Hidden if user doesn't have `view-notifications` permission

**Scorecard Menu:**
- [ ] Visible if user has `view-scorecard` permission
- [ ] Hidden if user doesn't have `view-scorecard` permission

**Testing Menu:**
- [ ] Visible if user has `access-testing-module` permission
- [ ] Hidden if user doesn't have `access-testing-module` permission
- [ ] Telegram submenu visible if user has `send-telegram-test-messages` permission

**Settings Menu:**
- [ ] Visible if user has `view-settings` permission
- [ ] Hidden if user doesn't have `view-settings` permission

**Expected Result:** Menu items show/hide based on permissions.

---

### Test 3.2: Button Visibility in Views

**Notifications Index Page:**
1. Login as user with different permissions
2. Check button visibility:

**Create Notification Button:**
- [ ] Visible if user has `create-notifications` permission
- [ ] Hidden if user doesn't have `create-notifications` permission

**Action Dropdown Buttons (in notification rows):**
- [ ] View button visible if user has `view-notification-details` permission
- [ ] Edit button visible if user has `edit-notifications` permission
- [ ] Delete button visible if user has `delete-notifications` permission

**Expected Result:** Buttons show/hide based on permissions.

---

## Test 4: Cache Update & Invalidation

### Test 4.1: Update User Roles

**Steps:**
1. Login as User A
2. Note current permissions (check cache or sidebar visibility)
3. In another session (as admin), update User A's roles
4. Refresh User A's session
5. Check if permissions updated

**Verify Cache Update:**
```bash
php artisan tinker
```
```php
$user = App\Models\User::find(USER_ID);
$permissions = $user->getCachedPermissions();
// Should reflect new permissions after role update
```

**Expected Result:**
- [ ] Cache is cleared when roles are updated
- [ ] Cache is reloaded with new permissions
- [ ] Sidebar menu items update immediately
- [ ] Route access reflects new permissions

---

### Test 4.2: Update Role Permissions

**Steps:**
1. Login as multiple users with Role X
2. Note their current permissions
3. As admin, update Role X's permissions
4. All users with Role X should have updated permissions

**Verify Cache Update:**
```php
// In tinker, check multiple users with the updated role
$role = App\Models\Role::where('slug', 'role-slug')->first();
$users = $role->users;

foreach ($users as $user) {
    echo "User: {$user->name}\n";
    $permissions = $user->getCachedPermissions();
    print_r($permissions);
    echo "\n";
}
```

**Expected Result:**
- [ ] All users with the role get cache cleared
- [ ] All users get updated permissions in cache
- [ ] All users see updated menu items immediately

---

## Test 5: Performance Testing

### Test 5.1: Check Database Queries

**Steps:**
1. Install Laravel Debugbar (if not installed): `composer require barryvdh/laravel-debugbar --dev`

2. Login and navigate through pages
3. Check Debugbar's "Queries" tab

**Expected Result:**
- [ ] No database queries for permission checks after initial login
- [ ] Permission checks use cached data only
- [ ] Only 1-2 queries per page load (for page data, not permissions)

**Alternative: Check with Telescope or Logging:**
```php
// In User model, you can add logging to verify
public function hasPermission($permission): bool
{
    \Log::info('Permission check', [
        'user_id' => $this->id,
        'permission' => $permission,
        'cache_used' => Cache::has($this->getPermissionsCacheKey())
    ]);
    // ... rest of method
}
```

---

### Test 5.2: Cache Performance

**Steps:**
1. Login as user
2. Make multiple permission checks rapidly
3. Verify cache is being used

```php
// In tinker or controller
$user = auth()->user();

// Multiple permission checks
$user->hasPermission('view-notifications'); // Should use cache
$user->hasPermission('create-notifications'); // Should use cache
$user->hasPermission('edit-notifications'); // Should use cache
```

**Expected Result:**
- [ ] All permission checks are fast (milliseconds)
- [ ] No noticeable delay in page loading
- [ ] Cache is hit for all permission checks

---

## Test 6: Super Admin Access

### Steps:
1. Login as user with `super-admin` role
2. Try to access all protected routes
   - [ ] Should have access to ALL routes
   - [ ] Should see ALL menu items
   - [ ] Should see ALL buttons

3. Verify Super Admin has all permissions:
```php
// In tinker
$superAdmin = App\Models\User::whereHas('roles', function($q) {
    $q->where('slug', 'super-admin');
})->first();

$superAdmin->hasPermission('view-notifications'); // Should return true
$superAdmin->hasPermission('create-notifications'); // Should return true
$superAdmin->hasPermission('any-random-permission'); // Should return true
```

**Expected Result:**
- [ ] Super Admin bypasses all permission checks
- [ ] Super Admin has access to everything

---

## Test 7: Edge Cases

### Test 7.1: User with No Roles

**Steps:**
1. Create a user with no roles assigned
2. Login as that user
3. Check access

**Expected Result:**
- [ ] User should not see any protected menu items
- [ ] User should get 403 on all protected routes
- [ ] Cache should be empty or contain empty arrays

---

### Test 7.2: Role with No Permissions

**Steps:**
1. Create a role with no permissions
2. Assign role to user
3. Login as that user
4. Check access

**Expected Result:**
- [ ] User should not see protected menu items
- [ ] User should get 403 on protected routes
- [ ] Cache should contain empty permissions array

---

### Test 7.3: Multiple Roles

**Steps:**
1. Assign multiple roles to a user
2. Each role has different permissions
3. Login as that user
4. Check access

**Expected Result:**
- [ ] User should have union of all permissions from all roles
- [ ] All menu items from all roles should be visible
- [ ] Cache should contain all permissions from all roles

---

## Test 8: Blade Directives

### Steps:
1. Create a test view with Blade directives:
```blade
@can('create-notifications')
    <p>User can create notifications</p>
@endcan

@hasRole('super-admin')
    <p>User is super admin</p>
@endhasRole
```

2. Login as different users and check output

**Expected Result:**
- [ ] `@can()` directive works correctly
- [ ] `@hasRole()` directive works correctly
- [ ] Content shows/hides based on permissions

---

## Quick Test Script

Run this in `php artisan tinker` to quickly test:

```php
// Get a test user
$user = App\Models\User::find(1); // Replace with actual user ID

// Test permission check
echo "Has view-notifications: " . ($user->hasPermission('view-notifications') ? 'YES' : 'NO') . "\n";
echo "Has create-notifications: " . ($user->hasPermission('create-notifications') ? 'YES' : 'NO') . "\n";

// Test role check
echo "Has manager role: " . ($user->hasRole('manager') ? 'YES' : 'NO') . "\n";

// Check cache
echo "Cached permissions: " . count($user->getCachedPermissions()) . " permissions\n";
echo "Cached roles: " . count($user->getCachedRoles()) . " roles\n";

// View permissions
print_r($user->getCachedPermissions());
print_r($user->getCachedRoles());
```

---

## Troubleshooting

### Cache Not Working?

1. **Check cache driver:**
   ```bash
   # In .env file
   CACHE_DRIVER=file  # or redis, memcached
   ```

2. **Clear cache:**
   ```bash
   php artisan cache:clear
   ```

3. **Manually check cache:**
   ```php
   Cache::get('user_permissions_1');
   ```

### Permissions Not Updating?

1. **Clear user cache manually:**
   ```php
   $user = App\Models\User::find(USER_ID);
   $user->clearPermissionCache();
   $user->loadPermissionsIntoCache();
   $user->loadRolesIntoCache();
   ```

2. **Check if roles/permissions are assigned:**
   ```php
   $user = App\Models\User::find(USER_ID);
   $user->roles; // Check roles
   $user->roles->first()->permissions; // Check permissions
   ```

### 403 Errors Everywhere?

1. **Verify user has roles:**
   ```php
   $user = App\Models\User::find(USER_ID);
   $user->roles; // Should return collection of roles
   ```

2. **Verify roles have permissions:**
   ```php
   $role = App\Models\Role::find(ROLE_ID);
   $role->permissions; // Should return collection of permissions
   ```

3. **Check permission slugs match exactly:**
   - Permission slug in database: `view-notifications`
   - Route middleware: `permission:view-notifications`
   - Must match exactly (case-sensitive)

---

## Test Summary Checklist

After completing all tests, verify:

- [ ] **Phase 1-7 Implementation Complete**
  - [ ] Cache system working
  - [ ] Middleware working
  - [ ] Routes protected
  - [ ] Views protected
  - [ ] Cache invalidation working

- [ ] **All Tests Passed**
  - [ ] Login & cache verification
  - [ ] Route protection (with/without permissions)
  - [ ] View display (menu items, buttons)
  - [ ] Cache updates correctly
  - [ ] Performance acceptable
  - [ ] Super admin access working
  - [ ] Edge cases handled

---

## Success Criteria

✅ **System is working correctly if:**
1. Users can only access routes they have permission for
2. Menu items and buttons show/hide based on permissions
3. Cache is created on login and updated when roles/permissions change
4. No database queries for permission checks after login
5. Super Admin has access to everything
6. Cache invalidation works automatically

---

**Test Date:** _________________

**Tested By:** _________________

**Status:** ⬜ Pass | ⬜ Fail | ⬜ Partial

**Notes:**
_________________________________________________
_________________________________________________
_________________________________________________

