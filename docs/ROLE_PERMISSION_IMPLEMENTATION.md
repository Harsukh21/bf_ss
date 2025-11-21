# Role & Permission Implementation Guide

## Overview
This guide provides a **phase-wise approach** to implementing role-based access control (RBAC) with **permission caching** to avoid database queries on every page load.

## Architecture Strategy

### Key Principles:
1. **Cache all user permissions on login** - Load once, use everywhere
2. **Update cache when roles/permissions change** - Keep data fresh
3. **Simple helper methods** - Easy to use in routes, controllers, and views
4. **Zero database queries for permission checks** - Use session/cache only

---

## Phase 1: Setup Permission Caching System

### Step 1.1: Update User Model with Cache Methods

**File:** `app/Models/User.php`

Add these methods to handle permission caching:

```php
use Illuminate\Support\Facades\Cache;

/**
 * Cache key prefix for user permissions
 */
public function getPermissionsCacheKey(): string
{
    return "user_permissions_{$this->id}";
}

/**
 * Cache key prefix for user roles
 */
public function getRolesCacheKey(): string
{
    return "user_roles_{$this->id}";
}

/**
 * Load and cache all user permissions (via roles)
 */
public function loadPermissionsIntoCache(): array
{
    $permissions = $this->roles()
        ->with('permissions')
        ->get()
        ->pluck('permissions')
        ->flatten()
        ->unique('id')
        ->pluck('slug')
        ->toArray();

    // Cache for 24 hours (1440 minutes)
    Cache::put($this->getPermissionsCacheKey(), $permissions, now()->addMinutes(1440));

    return $permissions;
}

/**
 * Load and cache all user roles
 */
public function loadRolesIntoCache(): array
{
    $roles = $this->roles()->pluck('slug')->toArray();

    // Cache for 24 hours
    Cache::put($this->getRolesCacheKey(), $roles, now()->addMinutes(1440));

    return $roles;
}

/**
 * Clear user permission cache
 */
public function clearPermissionCache(): void
{
    Cache::forget($this->getPermissionsCacheKey());
    Cache::forget($this->getRolesCacheKey());
}

/**
 * Get cached permissions (load if not cached)
 */
public function getCachedPermissions(): array
{
    return Cache::remember($this->getPermissionsCacheKey(), now()->addMinutes(1440), function () {
        return $this->loadPermissionsIntoCache();
    });
}

/**
 * Get cached roles (load if not cached)
 */
public function getCachedRoles(): array
{
    return Cache::remember($this->getRolesCacheKey(), now()->addMinutes(1440), function () {
        return $this->loadRolesIntoCache();
    });
}

/**
 * Check if user has permission (using cache)
 */
public function hasPermission($permission): bool
{
    // Super Admin has all permissions
    if ($this->hasRole('super-admin')) {
        return true;
    }

    $permissions = $this->getCachedPermissions();
    
    if (is_string($permission)) {
        return in_array($permission, $permissions);
    }
    
    // If permission is a model instance, check by slug
    if (is_object($permission) && method_exists($permission, 'getAttribute')) {
        return in_array($permission->slug, $permissions);
    }
    
    return false;
}

/**
 * Check if user has role (using cache)
 */
public function hasRole($role): bool
{
    $roles = $this->getCachedRoles();
    
    if (is_string($role)) {
        return in_array($role, $roles);
    }
    
    // If role is a model instance, check by slug
    if (is_object($role) && method_exists($role, 'getAttribute')) {
        return in_array($role->slug, $roles);
    }
    
    return false;
}
```

### Step 1.2: Update AuthController to Cache on Login

**File:** `app/Http/Controllers/AuthController.php`

Update the `login()` method to cache permissions after successful login:

```php
if (Auth::attempt($credentials, $remember)) {
    $request->session()->regenerate();
    
    $user = Auth::user();
    
    // Load permissions into cache on login
    $user->loadPermissionsIntoCache();
    $user->loadRolesIntoCache();
    
    // Track login information
    ProfileController::trackLogin($user, $request);
    
    return redirect()->intended('/dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
}
```

Also update Web PIN login:

```php
Auth::login($user, $request->has('remember'));
$request->session()->regenerate();

// Load permissions into cache on login
$user->loadPermissionsIntoCache();
$user->loadRolesIntoCache();

// Track login information
ProfileController::trackLogin($user, $request);

return redirect()->intended('/dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
```

---

## Phase 2: Create Permission Middleware

### Step 2.1: Create PermissionMiddleware

**Command:**
```bash
php artisan make:middleware CheckPermission
```

**File:** `app/Http/Middleware/CheckPermission.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->hasPermission($permission)) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
```

### Step 2.2: Register Middleware

**File:** `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'prevent.back' => \App\Http\Middleware\PreventBackAfterLogout::class,
        'permission' => \App\Http\Middleware\CheckPermission::class, // Add this line
    ]);
})
```

---

## Phase 3: Create Helper Functions (Optional but Recommended)

### Step 3.1: Create Permission Helper

**File:** `app/Helpers/PermissionHelper.php`

```php
<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if authenticated user has permission
     */
    public static function can(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasPermission($permission);
    }

    /**
     * Check if authenticated user has role
     */
    public static function hasRole(string $role): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole($role);
    }
}
```

### Step 3.2: Register Helper File (if using global helpers)

**File:** `composer.json`

Add to `autoload` section:

```json
"autoload": {
    "files": [
        "app/Helpers/PermissionHelper.php"
    ],
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    }
},
```

Then run: `composer dump-autoload`

---

## Phase 4: Implement in Routes

### Step 4.1: Protect Routes with Permission Middleware

**File:** `routes/web.php`

```php
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Testing\TelegramTestController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ScorecardController;

// Protected Routes
Route::middleware(['auth', 'prevent.back'])->group(function () {
    
    // Notifications - Protected by permissions
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])
            ->middleware('permission:view-notifications')
            ->name('index');
        
        Route::get('/create', [NotificationController::class, 'create'])
            ->middleware('permission:create-notifications')
            ->name('create');
        
        Route::post('/', [NotificationController::class, 'store'])
            ->middleware('permission:create-notifications')
            ->name('store');
        
        Route::get('/{notification}/edit', [NotificationController::class, 'edit'])
            ->middleware('permission:edit-notifications')
            ->name('edit');
        
        Route::put('/{notification}', [NotificationController::class, 'update'])
            ->middleware('permission:edit-notifications')
            ->name('update');
        
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])
            ->middleware('permission:delete-notifications')
            ->name('destroy');
        
        Route::get('/{notification}', [NotificationController::class, 'show'])
            ->middleware('permission:view-notification-details')
            ->name('show');
    });

    // Testing Module
    Route::prefix('testing')->name('testing.')->group(function () {
        Route::get('/telegram', [TelegramTestController::class, 'index'])
            ->middleware('permission:access-testing-module')
            ->name('telegram.index');
        
        Route::post('/telegram/send', [TelegramTestController::class, 'sendTestMessage'])
            ->middleware('permission:send-telegram-test-messages')
            ->name('telegram.send');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])
            ->middleware('permission:view-settings')
            ->name('index');
        
        Route::post('/', [SettingsController::class, 'store'])
            ->middleware('permission:create-settings')
            ->name('store');
        
        Route::put('/{setting}', [SettingsController::class, 'update'])
            ->middleware('permission:edit-settings')
            ->name('update');
        
        Route::delete('/{setting}', [SettingsController::class, 'destroy'])
            ->middleware('permission:delete-settings')
            ->name('destroy');
    });

    // Scorecard
    Route::prefix('scorecard')->name('scorecard.')->group(function () {
        Route::get('/', [ScorecardController::class, 'index'])
            ->middleware('permission:view-scorecard')
            ->name('index');
        
        Route::get('/events/{exEventId}/markets', [ScorecardController::class, 'getEventMarkets'])
            ->middleware('permission:view-event-markets')
            ->name('events.markets');
        
        Route::post('/events/{exEventId}/update', [ScorecardController::class, 'updateEvent'])
            ->middleware('permission:update-scorecard-events')
            ->name('events.update');
        
        Route::post('/events/{exEventId}/update-labels', [ScorecardController::class, 'updateLabels'])
            ->middleware('permission:update-scorecard-labels')
            ->name('events.update-labels');
    });
});
```

---

## Phase 5: Implement in Controllers

### Step 5.1: Check Permissions in Controller Methods

**File:** `app/Http/Controllers/NotificationController.php`

```php
use Illuminate\Support\Facades\Auth;

public function index()
{
    // Optional: Double-check permission (middleware already checks, but this is extra safety)
    if (!Auth::user()->hasPermission('view-notifications')) {
        abort(403, 'You do not have permission to view notifications.');
    }

    // Your existing code...
}

public function create()
{
    if (!Auth::user()->hasPermission('create-notifications')) {
        abort(403, 'You do not have permission to create notifications.');
    }

    // Your existing code...
}

public function store(Request $request)
{
    if (!Auth::user()->hasPermission('create-notifications')) {
        abort(403, 'You do not have permission to create notifications.');
    }

    // Your existing code...
}
```

**Note:** If you use middleware on routes, you can skip these checks in controllers. They're optional for extra safety.

---

## Phase 6: Implement in Blade Views

### Step 6.1: Hide/Show Menu Items Based on Permissions

**File:** `resources/views/layouts/partials/sidebar.blade.php`

```blade
{{-- Notifications Menu --}}
@if(auth()->user()->hasPermission('view-notifications'))
    <li class="nav-item">
        <a href="{{ route('notifications.index') }}" class="nav-link">
            <i class="nav-icon fas fa-bell"></i>
            <p>Notifications</p>
        </a>
    </li>
@endif

{{-- Testing Menu with Dropdown --}}
@if(auth()->user()->hasPermission('access-testing-module'))
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-flask"></i>
            <p>
                Testing
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            @if(auth()->user()->hasPermission('send-telegram-test-messages'))
                <li class="nav-item">
                    <a href="{{ route('testing.telegram.index') }}" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Telegram</p>
                    </a>
                </li>
            @endif
        </ul>
    </li>
@endif

{{-- Settings Menu --}}
@if(auth()->user()->hasPermission('view-settings'))
    <li class="nav-item">
        <a href="{{ route('settings.index') }}" class="nav-link">
            <i class="nav-icon fas fa-cog"></i>
            <p>Settings</p>
        </a>
    </li>
@endif

{{-- Scorecard Menu --}}
@if(auth()->user()->hasPermission('view-scorecard'))
    <li class="nav-item">
        <a href="{{ route('scorecard.index') }}" class="nav-link">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>Scorecard</p>
        </a>
    </li>
@endif
```

### Step 6.2: Hide/Show Buttons and Actions

**File:** `resources/views/notifications/index.blade.php`

```blade
@if(auth()->user()->hasPermission('create-notifications'))
    <a href="{{ route('notifications.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create Notification
    </a>
@endif

@foreach($notifications as $notification)
    <div class="card">
        <div class="card-body">
            {{-- Notification content --}}
        </div>
        <div class="card-footer">
            @if(auth()->user()->hasPermission('view-notification-details'))
                <a href="{{ route('notifications.show', $notification) }}" class="btn btn-sm btn-info">
                    View Details
                </a>
            @endif

            @if(auth()->user()->hasPermission('edit-notifications'))
                <a href="{{ route('notifications.edit', $notification) }}" class="btn btn-sm btn-warning">
                    Edit
                </a>
            @endif

            @if(auth()->user()->hasPermission('delete-notifications'))
                <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            @endif
        </div>
    </div>
@endforeach
```

### Step 6.3: Blade Directives (Optional)

**File:** `app/Providers/AppServiceProvider.php`

Add Blade directives for cleaner syntax:

```php
use Illuminate\Support\Facades\Blade;

public function boot(): void
{
    // Permission directive
    Blade::if('can', function ($permission) {
        return auth()->check() && auth()->user()->hasPermission($permission);
    });

    // Role directive
    Blade::if('hasRole', function ($role) {
        return auth()->check() && auth()->user()->hasRole($role);
    });
}
```

Then use in Blade:

```blade
@can('create-notifications')
    <a href="{{ route('notifications.create') }}" class="btn btn-primary">Create</a>
@endcan

@hasRole('super-admin')
    <a href="{{ route('admin.settings') }}" class="btn btn-danger">Admin Panel</a>
@endhasRole
```

---

## Phase 7: Cache Invalidation

### Step 7.1: Clear Cache When Roles/Permissions Change

**File:** `app/Http/Controllers/UserController.php`

When updating user roles:

```php
public function updateRoles(Request $request, User $user)
{
    // Your existing code to update roles...
    
    // Clear permission cache after role update
    $user->clearPermissionCache();
    
    // Reload cache with new permissions
    $user->loadPermissionsIntoCache();
    $user->loadRolesIntoCache();
    
    return redirect()->back()->with('success', 'User roles updated successfully.');
}
```

**File:** `app/Http/Controllers/RoleController.php`

When updating role permissions:

```php
use Illuminate\Support\Facades\DB;

public function update(Request $request, Role $role)
{
    // Your existing code to update role permissions...
    
    // Clear cache for all users with this role
    $users = $role->users;
    foreach ($users as $user) {
        $user->clearPermissionCache();
        $user->loadPermissionsIntoCache();
        $user->loadRolesIntoCache();
    }
    
    return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
}
```

---

## Phase 8: Testing Checklist

### Testing Steps:

1. **Login Test**
   - [ ] Login as different users
   - [ ] Check cache is created: `php artisan tinker` → `Cache::get('user_permissions_1')`
   - [ ] Or use: `php artisan permissions:test 1`

2. **Route Protection Test**
   - [ ] Try accessing route without permission → Should get 403
   - [ ] Try accessing route with permission → Should work

3. **Blade Display Test**
   - [ ] Menu items show/hide correctly
   - [ ] Buttons show/hide correctly

4. **Cache Update Test**
   - [ ] Update user roles → Cache should refresh
   - [ ] Update role permissions → Cache should refresh for all affected users

5. **Performance Test**
   - [ ] Check no database queries for permission checks (use Laravel Debugbar or Telescope)

### Testing Tools:

**Quick Test Command:**
```bash
# List all users
php artisan permissions:test

# Test specific user
php artisan permissions:test 1

# Test specific permission
php artisan permissions:test 1 --permission=view-notifications

# Test specific role
php artisan permissions:test 1 --role=manager
```

**Detailed Testing Checklist:**
See `TESTING_CHECKLIST.md` for comprehensive testing guide.

---

## Implementation Order Summary

1. ✅ **Phase 1**: Update User Model with cache methods - **COMPLETE**
2. ✅ **Phase 2**: Create Permission Middleware - **COMPLETE**
3. ✅ **Phase 3**: Create helper functions - **COMPLETE**
4. ✅ **Phase 4**: Protect routes with middleware - **COMPLETE**
5. ✅ **Phase 5**: Add checks in controllers (optional) - **SKIPPED** (Using middleware instead)
6. ✅ **Phase 6**: Update Blade views - **COMPLETE**
7. ✅ **Phase 7**: Implement cache invalidation - **COMPLETE**
8. ✅ **Phase 8**: Test everything - **COMPLETE** (Use `TESTING_CHECKLIST.md` and `php artisan permissions:test`)

---

## Quick Reference

### Permission Slugs (from PermissionSeeder)

**Notifications:**
- `view-notifications`
- `create-notifications`
- `edit-notifications`
- `delete-notifications`
- `view-notification-details`
- `mark-notifications-as-read`
- `view-pending-notifications`
- `manage-push-notifications`

**Settings:**
- `view-settings`
- `create-settings`
- `edit-settings`
- `delete-settings`

**Scorecard:**
- `view-scorecard`
- `view-event-markets`
- `update-scorecard-events`
- `update-scorecard-labels`

**Testing:**
- `access-testing-module`
- `send-telegram-test-messages`

---

## Best Practices

1. ✅ **Always cache on login** - Never load permissions from DB on every request
2. ✅ **Clear cache when permissions change** - Keep data fresh
3. ✅ **Use middleware for route protection** - Consistent security
4. ✅ **Double-check in controllers for sensitive operations** - Extra safety
5. ✅ **Hide UI elements in Blade** - Better UX, but not security
6. ✅ **Test with different roles** - Ensure proper access control

---

## Troubleshooting

### Cache not working?
- Check cache driver in `.env`: `CACHE_DRIVER=file` (or `redis`, `memcached`)
- Clear cache: `php artisan cache:clear`

### Permissions not updating?
- Ensure `clearPermissionCache()` is called when roles/permissions change
- Manually clear: `php artisan tinker` → `Cache::forget('user_permissions_1')`

### 403 errors everywhere?
- Check user has roles assigned
- Check roles have permissions assigned
- Verify permission slugs match exactly

---

**Ready to implement?** Start with Phase 1 and work through each phase sequentially!

