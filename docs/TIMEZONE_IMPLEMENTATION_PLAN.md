# Timezone Implementation Plan

## Overview
This document outlines the plan to implement user-based timezone conversion **for display purposes only**. The database will **always store times in IST (Indian Standard Time, UTC+5:30)** regardless of user timezone. Timezone conversion is applied only when:
1. **Displaying data** - Convert IST from database to user's timezone
2. **User input** - Convert user's timezone input to IST before saving to database

## Core Principle
- **Database**: Always stores IST (UTC+5:30) - NO changes to storage format
- **Login**: Get user's timezone from database and store in session
- **Display**: Use timezone from session to convert IST to user's timezone for viewing
- **Input/Update**: Get current user's timezone, convert user input to IST, then save to database

## Current State
- **Database Storage**: All timestamps stored in IST (UTC+5:30) ✅ **Keep as-is**
- **User Timezone**: Available in `users.timezone` column (nullable)
- **Affected Modules**: 
  - Events (eventTime, marketTime, createdAt)
  - Markets (marketTime, createdAt, updatedAt)
  - Market Rates (created_at, updated_at)
  - SS Lists / Scorecard (marketTime, first_market_time)
  - Risk Markets (completeTime, marketTime)
  - User Activity (last_login_at, created_at, etc.)

## Implementation Strategy

### Phase 1: Login-Based Timezone Setup

#### 1.1 Store Timezone on Login
**File**: `app/Http/Controllers/AuthController.php`

**Update `login()` method**:
```php
public function login(Request $request)
{
    // ... existing validation ...
    
    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();
        
        $user = Auth::user();
        
        // ✅ NEW: Store user's timezone in session for performance
        $userTimezone = $user->timezone ?? 'Asia/Kolkata'; // Default to IST
        $request->session()->put('user_timezone', $userTimezone);
        
        // Track login information
        ProfileController::trackLogin($user, $request);
        
        return redirect()->intended('/dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
    }
    // ... existing code ...
}
```

**Update `logout()` method**:
```php
public function logout(Request $request)
{
    // Clear timezone from session
    $request->session()->forget('user_timezone');
    
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    // ... existing code ...
}
```

#### 1.2 Timezone Helper (Uses Session)
**File**: `app/Helpers/TimezoneHelper.php`
```php
class TimezoneHelper
{
    /**
     * Get current user's timezone from session or database
     * Priority: Session > Auth User > Default IST
     */
    public static function getUserTimezone($user = null)
    {
        // First check session (fastest - cached on login)
        $sessionTimezone = session('user_timezone');
        if ($sessionTimezone) {
            return $sessionTimezone;
        }
        
        // Fallback to user from database
        $user = $user ?? auth()->user();
        if ($user && $user->timezone) {
            // Store in session for future requests
            session(['user_timezone' => $user->timezone]);
            return $user->timezone;
        }
        
        // Default to IST
        return 'Asia/Kolkata';
    }
    
    /**
     * Get default timezone (IST)
     */
    public static function getDefaultTimezone()
    {
        return 'Asia/Kolkata';
    }
    
    /**
     * Check if user has timezone set
     */
    public static function isUserTimezoneSet($user = null)
    {
        $timezone = self::getUserTimezone($user);
        return $timezone !== 'Asia/Kolkata' || ($user && $user->timezone);
    }
}
```

#### 1.3 Timezone Service (Uses Session for Display, User for Input)
**File**: `app/Services/TimezoneService.php`
```php
use Carbon\Carbon;
use App\Helpers\TimezoneHelper;

class TimezoneService
{
    /**
     * Convert IST (from database) to user's timezone for DISPLAY
     * Uses timezone from session (set on login)
     * Input: IST datetime from database
     * Output: Datetime in user's timezone (from session)
     */
    public function convertForDisplay($istDateTime, $user = null, $format = null)
    {
        if (!$istDateTime) {
            return null;
        }
        
        // Get timezone from session (fastest - cached on login)
        $userTimezone = TimezoneHelper::getUserTimezone($user);
        
        // Parse IST time from database
        $carbon = Carbon::parse($istDateTime, 'Asia/Kolkata');
        
        // Convert to user's timezone (from session)
        $converted = $carbon->setTimezone($userTimezone);
        
        // Format if requested
        if ($format) {
            return $converted->format($format);
        }
        
        return $converted;
    }
    
    /**
     * Convert user input from their timezone to IST for DATABASE
     * Gets current user's timezone from auth()->user()
     * Input: Datetime in user's timezone (what user entered)
     * Output: IST datetime for database storage
     */
    public function convertToDatabase($userDateTime, $user = null)
    {
        if (!$userDateTime) {
            return null;
        }
        
        // Get current user's timezone (from auth user, not session)
        $user = $user ?? auth()->user();
        $userTimezone = $user ? ($user->timezone ?? 'Asia/Kolkata') : 'Asia/Kolkata';
        
        // Parse user's timezone input
        $carbon = Carbon::parse($userDateTime, $userTimezone);
        
        // Convert to IST for database
        $ist = $carbon->setTimezone('Asia/Kolkata');
        
        return $ist->format('Y-m-d H:i:s'); // Return formatted string for DB
    }
    
    /**
     * Format datetime for display in user's timezone (from session)
     */
    public function formatForDisplay($istDateTime, $user = null, $format = 'M d, Y h:i A')
    {
        return $this->convertForDisplay($istDateTime, $user, $format);
    }
    
    /**
     * Convert array of datetime fields for display
     */
    public function convertCollectionForDisplay($collection, $fields = [], $user = null)
    {
        return $collection->map(function ($item) use ($fields, $user) {
            foreach ($fields as $field) {
                if (isset($item->$field)) {
                    $item->{$field . '_display'} = $this->formatForDisplay($item->$field, $user);
                }
            }
            return $item;
        });
    }
}
```

**Key Rules**:
- **Display (`convertForDisplay()`)**: 
  - Uses timezone from **session** (set on login)
  - Converts IST → User Timezone (for viewing)
  
- **Input/Update (`convertToDatabase()`)**: 
  - Gets timezone from **current authenticated user** (`auth()->user()`)
  - Converts User Timezone → IST (before saving to DB)

#### 1.4 Blade Helper Functions
**File**: `app/Providers/AppServiceProvider.php`
```php
Blade::directive('userTimezone', function () {
    return "<?php echo app(\App\Helpers\TimezoneHelper::class)->getUserTimezone(); ?>";
});

Blade::directive('convertTime', function ($expression) {
    return "<?php echo app(\App\Services\TimezoneService::class)->convertForDisplay($expression); ?>";
});

Blade::directive('formatTime', function ($expression) {
    return "<?php echo app(\App\Services\TimezoneService::class)->formatForDisplay($expression); ?>";
});
```

### Phase 2: View Layer Updates (Display Only)

#### 2.1 Events Module
**Files to Update**:
- `resources/views/events/index.blade.php`
- `resources/views/events/all.blade.php`
- `resources/views/events/show.blade.php`

**Fields to Convert for Display**:
- `marketTime` → `@formatTime($event->marketTime)`
- `eventTime` → `@formatTime($event->eventTime)`
- `createdAt` → `@formatTime($event->createdAt)`

**Date Filters**:
- User selects date in their timezone → Controller converts to IST range for query
- Results from database (IST) → Convert to user timezone for display

#### 2.2 Markets Module
**Files to Update**:
- `resources/views/markets/index.blade.php`
- `resources/views/markets/all.blade.php`

**Fields to Convert for Display**:
- `marketTime` → `@formatTime($market->marketTime)`
- `createdAt` → `@formatTime($market->createdAt)`

#### 2.3 Market Rates Module
**Files to Update**:
- `resources/views/market-rates/index.blade.php`
- `resources/views/market-rates/show.blade.php`

**Fields to Convert for Display**:
- `created_at` → `@formatTime($marketRate->created_at)`
- `updated_at` → `@formatTime($marketRate->updated_at)`

#### 2.4 Scorecard Module
**Files to Update**:
- `resources/views/scorecard/index.blade.php`

**Fields to Convert for Display**:
- `marketTime` → `@formatTime($event->marketTime)`
- `first_market_time` → `@formatTime($event->first_market_time)`

#### 2.5 Risk Module
**Files to Update**:
- `resources/views/risk/index.blade.php`

**Fields to Convert for Display**:
- `completeTime` → `@formatTime($market->completeTime)`
- `marketTime` → `@formatTime($market->marketTime)`

#### 2.6 User Module
**Files to Update**:
- `resources/views/users/index.blade.php`
- `resources/views/users/show.blade.php`

**Fields to Convert for Display**:
- `last_login_at` → `@formatTime($user->last_login_at)`
- `created_at` → `@formatTime($user->created_at)`

### Phase 3: Controller Layer Updates

#### 3.1 Input Conversion (User Timezone → IST)
**Before Saving to Database**:
```php
use App\Services\TimezoneService;

// In store/update methods
$timezoneService = app(TimezoneService::class);
// ✅ Get current authenticated user's timezone (from DB, not session)
$user = auth()->user(); // Must get fresh user for insert/update

// Convert user input from their timezone to IST
$istMarketTime = $timezoneService->convertToDatabase(
    $request->input('market_time'), 
    $user // Pass user to get timezone from database
);

// Save IST to database
$event->marketTime = $istMarketTime;
$event->save(); // Always saves in IST

// ✅ Important: After update, refresh session timezone if user updated their timezone
if ($request->has('timezone')) {
    session(['user_timezone' => $request->input('timezone')]);
}
```

#### 3.2 Output Conversion (IST → User Timezone)
**Before Display**:
```php
use App\Services\TimezoneService;

// In index/show methods
$timezoneService = app(TimezoneService::class);
// ✅ No need to pass user - service uses session timezone (set on login)

$events = Event::all();

// Convert each event's times for display (uses session timezone)
$events->transform(function ($event) use ($timezoneService) {
    $event->marketTime_display = $timezoneService->formatForDisplay(
        $event->marketTime // No user needed - uses session
    );
    $event->createdAt_display = $timezoneService->formatForDisplay(
        $event->createdAt // No user needed - uses session
    );
    return $event;
});
```

#### 3.3 Filter Conversion
**Date Range Filters**:
```php
use App\Services\TimezoneService;

$timezoneService = app(TimezoneService::class);
// ✅ For filter input: Get current user's timezone from auth (for conversion to IST)
$user = auth()->user();

// User selects date in their timezone (from session - what they see)
$userStartDate = $request->input('date_from'); // User's timezone
$userEndDate = $request->input('date_to');     // User's timezone

// ✅ Convert user input to IST for database query
$istStartDate = $timezoneService->convertToDatabase($userStartDate, $user);
$istEndDate = $timezoneService->convertToDatabase($userEndDate, $user);

// Query database (always queries IST)
$events = Event::whereBetween('marketTime', [$istStartDate, $istEndDate])->get();

// ✅ Convert results back to user timezone for display (uses session)
$events->transform(function ($event) use ($timezoneService) {
    $event->marketTime_display = $timezoneService->formatForDisplay(
        $event->marketTime // Uses session timezone automatically
    );
    return $event;
});
```

#### 3.4 Controllers to Update
1. **EventController.php**
   - `index()`, `all()`, `show()`: Convert IST to user timezone for display
   - `store()`, `update()`, `updateMarketTime()`: Convert user input to IST before saving
   - `buildEventFilterSql()`: Convert filter dates to IST range

2. **MarketController.php**
   - Same approach as EventController

3. **MarketRateController.php**
   - Convert timestamps for display only

4. **ScorecardController.php**
   - Convert market times for display

5. **RiskController.php**
   - Convert completeTime and marketTime for display
   - Convert filter dates to IST before querying

6. **UserController.php**
   - Convert user activity times for display

### Phase 4: Database Layer (NO Changes)

#### 4.1 Database Storage Rules
- ✅ **Keep all timestamps in IST** - No schema changes
- ✅ **No timezone column needed** in event/market tables
- ✅ **All inserts/updates** automatically save IST
- ✅ **All queries** always use IST

#### 4.2 Migration Strategy
- **No migrations needed** - Database remains as-is
- Only application layer changes

### Phase 5: Timezone Conversion Logic

#### 5.1 Carbon Usage for Display (Uses Session)
```php
use Carbon\Carbon;
use App\Helpers\TimezoneHelper;

/**
 * Convert IST (from DB) to User Timezone for DISPLAY
 * Uses timezone from session (set on login)
 */
public function convertForDisplay($istDateTime, $user = null, $format = null)
{
    if (!$istDateTime) {
        return null;
    }
    
    // ✅ Get timezone from session (fastest - cached on login)
    $userTimezone = TimezoneHelper::getUserTimezone($user);
    
    // Parse IST time from database
    $carbon = Carbon::parse($istDateTime, 'Asia/Kolkata');
    
    // Convert to user's timezone (from session)
    $converted = $carbon->setTimezone($userTimezone);
    
    // Format if requested
    if ($format) {
        return $converted->format($format);
    }
    
    return $converted;
}
```

#### 5.2 Carbon Usage for Input (Save to DB - Uses Auth User)
```php
use Carbon\Carbon;

/**
 * Convert User Timezone to IST for DATABASE storage
 * Gets timezone from current authenticated user (not session)
 */
public function convertToDatabase($userDateTime, $user = null)
{
    if (!$userDateTime) {
        return null;
    }
    
    // ✅ Get current user's timezone from database (not session)
    // Important: For insert/update, always get fresh user timezone
    $user = $user ?? auth()->user();
    $userTimezone = $user ? ($user->timezone ?? 'Asia/Kolkata') : 'Asia/Kolkata';
    
    // Parse user's timezone input (what user entered in their timezone)
    $carbon = Carbon::parse($userDateTime, $userTimezone);
    
    // Convert to IST for database storage
    $ist = $carbon->setTimezone('Asia/Kolkata');
    
    return $ist->format('Y-m-d H:i:s'); // Return formatted string for DB
}
```

### Phase 6: Frontend (Display Labels)

#### 6.1 Show Timezone Label
Add timezone indicator next to times in views:
```blade
<span class="text-xs text-gray-500">
    @formatTime($event->marketTime) 
    <span class="ml-1">({{ @userTimezone }})</span>
</span>
```

#### 6.2 Date Picker Timezone
- Show timezone in date picker labels
- No changes to date picker functionality (server handles conversion)

### Phase 7: CSV Export

#### 7.1 Export in User Timezone
```php
// In export methods
$timezoneService = app(TimezoneService::class);
$user = auth()->user();

foreach ($events as $event) {
    $row = [
        'Event Time' => $timezoneService->formatForDisplay(
            $event->marketTime, 
            $user,
            'Y-m-d H:i:s'
        ),
        // ... other fields
    ];
    fputcsv($handle, $row);
}
```

## Implementation Steps

### Step 1: Login Integration & Helper & Service (Day 1)
1. Update `AuthController@login()` to store timezone in session
2. Update `AuthController@logout()` to clear timezone from session
3. Create `app/Helpers/TimezoneHelper.php` (uses session)
4. Create `app/Services/TimezoneService.php` (session for display, user for input)
5. Register service in `AppServiceProvider.php`
6. Add Blade directives

### Step 2: Test Helper Functions (Day 1)
1. Test `convertForDisplay()` with various timezones
2. Test `convertToDatabase()` with various timezones
3. Verify IST conversion accuracy

### Step 3: Update Controllers - Display (Days 2-3)
1. Update `EventController` - convert results for display
2. Update `MarketController` - convert results for display
3. Update `MarketRateController` - convert timestamps
4. Update `ScorecardController` - convert times
5. Update `RiskController` - convert times
6. Update `UserController` - convert activity times

### Step 4: Update Controllers - Input (Days 3-4)
1. Update `EventController` - convert input to IST before save
2. Update `MarketController` - convert input to IST before save
3. Update `RiskController` - convert filter dates to IST
4. Test all insert/update operations

### Step 5: Update Views (Days 4-6)
1. Update Events views - use `@formatTime()` directive
2. Update Markets views
3. Update Market Rates views
4. Update Scorecard views
5. Update Risk views
6. Update User views
7. Add timezone labels where appropriate

### Step 6: Update CSV Exports (Day 6)
1. Update `EventController@export`
2. Update `MarketController@export`
3. Test exports with different timezones

### Step 7: Testing (Days 7-8)
1. Test with IST timezone (should show same as before)
2. Test with EST timezone
3. Test with PST timezone
4. Test with Asian timezones
5. Test date filters with different timezones
6. Test insert/update operations
7. Test CSV exports
8. Test edge cases (DST transitions)

## Important Rules

### ✅ DO:
- Convert IST → User Timezone for **DISPLAY only**
- Convert User Timezone → IST for **INPUT before saving**
- Always save IST to database
- Always query IST from database
- Use Carbon for all conversions
- Test with multiple timezones

### ❌ DON'T:
- Change database timezone column types
- Store user timezone in event/market tables
- Convert at database level
- Change existing database timestamps
- Mix timezone conversions in queries

## Technical Implementation Details

### Carbon Conversion Examples

**Display (IST → User Timezone)**:
```php
// Database has: 2025-11-19 14:30:00 (IST)
$dbTime = '2025-11-19 14:30:00';
$carbon = Carbon::parse($dbTime, 'Asia/Kolkata'); // Parse as IST
$userTime = $carbon->setTimezone('America/New_York'); // Convert to EST
// Result: 2025-11-19 04:00:00 EST
```

**Input (User Timezone → IST)**:
```php
// User enters: 2025-11-19 12:00:00 (EST)
$userInput = '2025-11-19 12:00:00';
$carbon = Carbon::parse($userInput, 'America/New_York'); // Parse as EST
$istTime = $carbon->setTimezone('Asia/Kolkata'); // Convert to IST
// Result: 2025-11-19 22:30:00 IST (save this to DB)
```

## Performance Considerations

### Optimization Strategies
- Cache user timezone in session (once per request)
- Use collection mapping for bulk conversions
- Consider lazy loading for timezone conversions
- Use database indexes on timestamp columns (already in place)

### Caching Example
```php
// In middleware or controller
$userTimezone = cache()->remember(
    "user_timezone_{$user->id}",
    now()->addHours(24),
    fn() => $user->timezone ?? 'Asia/Kolkata'
);
```

## Files to Create/Modify

### New Files
- ✅ `config/timezones.php` (already created)
- `app/Helpers/TimezoneHelper.php`
- `app/Services/TimezoneService.php`

### Modified Files
- ✅ `app/Http/Controllers/AuthController.php` (store timezone in session on login)
- `app/Providers/AppServiceProvider.php` (add Blade directives)
- `app/Http/Controllers/EventController.php`
- `app/Http/Controllers/MarketController.php`
- `app/Http/Controllers/MarketRateController.php`
- `app/Http/Controllers/ScorecardController.php`
- `app/Http/Controllers/RiskController.php`
- `app/Http/Controllers/UserController.php`
- All view files (events, markets, risk, scorecard, etc.)
- ✅ `resources/views/users/edit.blade.php` (already done)

## Success Criteria

- [x] User can select timezone in profile/edit page
- [ ] Timezone stored in session on login
- [ ] Timezone cleared from session on logout
- [ ] All times display using timezone from session
- [ ] All inserts/updates use current user's timezone from database
- [ ] Database always stores IST (verified)
- [ ] Date filters work correctly (user input → IST for query)
- [ ] All inserts/updates save IST to database
- [ ] CSV exports use user timezone from session
- [ ] No performance degradation (session caching)
- [ ] Default behavior (IST) maintained for users without preference
- [ ] All edge cases handled (DST, etc.)
- [ ] Session refreshed when user updates timezone

## Testing Checklist

### Display Tests
- [ ] User in IST sees correct times
- [ ] User in EST sees converted times
- [ ] User in PST sees converted times
- [ ] User without timezone defaults to IST
- [ ] All date/time fields display correctly

### Input Tests
- [ ] User in EST can create event (saves IST correctly)
- [ ] User in PST can update market time (saves IST correctly)
- [ ] Date filters work with EST timezone
- [ ] Date filters work with PST timezone

### Database Tests
- [ ] All new records saved in IST
- [ ] All updates saved in IST
- [ ] No existing data changed

### Export Tests
- [ ] CSV export shows user timezone
- [ ] CSV export accuracy verified

## Estimated Timeline

- **Step 1**: 1 day (Helper & Service)
- **Step 2**: 0.5 day (Testing helpers)
- **Steps 3-4**: 2 days (Controller updates)
- **Step 5**: 2-3 days (View updates)
- **Step 6**: 0.5 day (CSV exports)
- **Step 7**: 1-2 days (Testing)

**Total**: 7-9 days

## Notes

- This is a **view-layer only** implementation
- Database structure remains unchanged
- All timestamps in database stay in IST
- Conversions happen at application layer only
- Safe to rollback - no database changes
- Can be tested incrementally per module
- User experience: Times appear in their timezone, database stores IST
