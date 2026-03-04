# Attendance Module - Implementation Wiki

**Project:** DailyOps247 (bf_ss)
**Module:** Attendance & Leave Management
**Laravel Version:** 12.x | **PHP:** 8.2+

---

## Table of Contents

1. [Module Overview](#module-overview)
2. [Features](#features)
3. [Database Schema](#database-schema)
4. [Permissions](#permissions)
5. [Phase 1 – Database Migrations](#phase-1--database-migrations)
6. [Phase 2 – Models](#phase-2--models)
7. [Phase 3 – Controllers](#phase-3--controllers)
8. [Phase 4 – Routes](#phase-4--routes)
9. [Phase 5 – Views](#phase-5--views)
10. [Phase 6 – Sidebar & Navigation](#phase-6--sidebar--navigation)
11. [Phase 7 – Permissions & Roles](#phase-7--permissions--roles)
12. [Business Logic](#business-logic)

---

## Module Overview

The Attendance module allows employees to track their daily work hours by recording:
- **Login Time** – When the employee starts working
- **Break Start Time** – When the employee begins a break
- **Break End Time** – When the employee resumes work
- **Logout Time** – When the employee ends their workday

The system automatically calculates **net working hours** (total time minus break duration).

Additionally, the module manages:
- **Leave Requests** – Employees can apply for different leave types
- **Holiday Management** – Admins can define a pre-set holiday calendar
- **Admin Dashboard** – Full visibility and control over all attendance data

---

## Features

### Employee Features
- Clock in (Login Time) for the day
- Start / End break times
- Clock out (Logout Time)
- View own attendance history with calculated hours
- Request leave (L / SL / H)
- View leave request status (pending / approved / rejected)
- View holiday calendar

### Admin / Super Admin Features
- View all employees' attendance records
- Filter attendance by user, date range, status
- Add / Edit / Delete holidays (pre-holiday list)
- Approve or reject leave requests
- View attendance summary reports (hours per employee)
- Mark attendance manually for employees
- Export attendance data

---

## Database Schema

### Table: `attendances`

| Column | Type | Description |
|---|---|---|
| id | BIGINT (PK) | Auto increment |
| user_id | BIGINT (FK) | References users.id |
| date | DATE | Attendance date |
| login_time | TIME NULL | Clock-in time |
| break_start_time | TIME NULL | Break start time |
| break_end_time | TIME NULL | Break end time |
| logout_time | TIME NULL | Clock-out time |
| total_hours | DECIMAL(5,2) NULL | Net working hours |
| break_duration | DECIMAL(5,2) NULL | Break duration in hours |
| status | ENUM | present / absent / half_day / on_leave / holiday |
| notes | TEXT NULL | Admin notes |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Indexes:** `user_id`, `date`, unique(`user_id`, `date`)

---

### Table: `leaves`

| Column | Type | Description |
|---|---|---|
| id | BIGINT (PK) | Auto increment |
| user_id | BIGINT (FK) | References users.id |
| leave_type | ENUM | L / SL / H / CO (see below) |
| from_date | DATE | Leave start date |
| to_date | DATE | Leave end date |
| total_days | DECIMAL(4,1) | Total leave days |
| reason | TEXT | Employee's reason |
| status | ENUM | pending / approved / rejected / cancelled |
| approved_by | BIGINT (FK) NULL | References users.id |
| approved_at | TIMESTAMP NULL | When it was approved/rejected |
| admin_notes | TEXT NULL | Admin's notes on the leave |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Leave Types:**
- `L` – Casual Leave (full/half day off)
- `SL` – Sick Leave (medical leave)
- `H` – Holiday (applied holiday from calendar)
- `CO` – Compensatory Off

---

### Table: `holidays`

| Column | Type | Description |
|---|---|---|
| id | BIGINT (PK) | Auto increment |
| name | VARCHAR(255) | Holiday name (e.g., "Diwali") |
| date | DATE | Holiday date |
| description | TEXT NULL | Optional description |
| is_recurring | BOOLEAN | Repeats every year on same date |
| created_by | BIGINT (FK) | References users.id |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

## Permissions

| Permission Key | Description |
|---|---|
| `view-attendance` | View own attendance records |
| `mark-attendance` | Clock in/out |
| `view-all-attendance` | View all employees' attendance (admin) |
| `manage-attendance` | Manually update any attendance record |
| `view-leaves` | View own leaves |
| `create-leaves` | Submit leave requests |
| `manage-leaves` | Approve / reject / manage all leaves (admin) |
| `view-holidays` | View holiday calendar |
| `manage-holidays` | Create / edit / delete holidays (admin) |

---

## Phase 1 – Database Migrations

**Files to create:**
1. `database/migrations/YYYY_MM_DD_create_attendances_table.php`
2. `database/migrations/YYYY_MM_DD_create_leaves_table.php`
3. `database/migrations/YYYY_MM_DD_create_holidays_table.php`

**Steps:**
1. Create migration files using artisan
2. Define schema per the Database Schema section above
3. Run `php artisan migrate`

**Artisan Commands:**
```bash
php artisan make:migration create_attendances_table
php artisan make:migration create_leaves_table
php artisan make:migration create_holidays_table
php artisan migrate
```

---

## Phase 2 – Models

**Files to create:**
1. `app/Models/Attendance.php`
2. `app/Models/Leave.php`
3. `app/Models/Holiday.php`

**Attendance Model Key Methods:**
- `calculateTotalHours()` – Returns net hours after subtracting break
- `getBreakDurationAttribute()` – Break duration in hours
- `scopeForDate($query, $date)` – Filter by date
- `scopeForUser($query, $userId)` – Filter by user

**Relationships:**
- `Attendance` belongsTo `User`
- `Leave` belongsTo `User` (requester)
- `Leave` belongsTo `User` (approver, via `approved_by`)
- `Holiday` belongsTo `User` (creator)
- `User` hasMany `Attendance`
- `User` hasMany `Leave`

---

## Phase 3 – Controllers

**Files to create:**
1. `app/Http/Controllers/AttendanceController.php`
2. `app/Http/Controllers/LeaveController.php`
3. `app/Http/Controllers/HolidayController.php`

### AttendanceController Methods:
| Method | Route | Description |
|---|---|---|
| `index()` | GET /attendance | Own attendance list |
| `today()` | GET /attendance/today | Today's attendance card |
| `clockIn()` | POST /attendance/clock-in | Record login time |
| `breakStart()` | POST /attendance/break-start | Record break start |
| `breakEnd()` | POST /attendance/break-end | Record break end |
| `clockOut()` | POST /attendance/clock-out | Record logout time |
| `adminIndex()` | GET /attendance/admin | All users attendance |
| `adminShow()` | GET /attendance/admin/{user} | Single user attendance |
| `adminEdit()` | GET /attendance/{id}/edit | Edit form |
| `adminUpdate()` | PUT /attendance/{id} | Update record |

### LeaveController Methods:
| Method | Route | Description |
|---|---|---|
| `index()` | GET /leaves | Own leave list |
| `create()` | GET /leaves/create | Leave request form |
| `store()` | POST /leaves | Submit leave |
| `show()` | GET /leaves/{id} | Leave detail |
| `cancel()` | POST /leaves/{id}/cancel | Cancel pending leave |
| `adminIndex()` | GET /leaves/admin | All leave requests |
| `approve()` | POST /leaves/{id}/approve | Approve leave |
| `reject()` | POST /leaves/{id}/reject | Reject leave |

### HolidayController Methods:
| Method | Route | Description |
|---|---|---|
| `index()` | GET /holidays | View holiday calendar |
| `create()` | GET /holidays/create | Add holiday form |
| `store()` | POST /holidays | Save holiday |
| `edit()` | GET /holidays/{id}/edit | Edit holiday form |
| `update()` | PUT /holidays/{id} | Update holiday |
| `destroy()` | DELETE /holidays/{id} | Delete holiday |

---

## Phase 4 – Routes

**File to update:** `routes/web.php`

Add the following route groups inside the authenticated middleware group:

```php
// Attendance Routes
Route::middleware(['auth'])->prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::get('/today', [AttendanceController::class, 'today'])->name('today');
    Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
    Route::post('/break-start', [AttendanceController::class, 'breakStart'])->name('break-start');
    Route::post('/break-end', [AttendanceController::class, 'breakEnd'])->name('break-end');
    Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');
    // Admin routes
    Route::middleware('permission:view-all-attendance')->group(function () {
        Route::get('/admin', [AttendanceController::class, 'adminIndex'])->name('admin.index');
        Route::get('/admin/{user}', [AttendanceController::class, 'adminShow'])->name('admin.show');
    });
    Route::middleware('permission:manage-attendance')->group(function () {
        Route::get('/{id}/edit', [AttendanceController::class, 'adminEdit'])->name('edit');
        Route::put('/{id}', [AttendanceController::class, 'adminUpdate'])->name('update');
    });
});

// Leave Routes
Route::middleware(['auth'])->prefix('leaves')->name('leaves.')->group(function () {
    Route::get('/', [LeaveController::class, 'index'])->name('index');
    Route::get('/create', [LeaveController::class, 'create'])->name('create');
    Route::post('/', [LeaveController::class, 'store'])->name('store');
    Route::get('/{id}', [LeaveController::class, 'show'])->name('show');
    Route::post('/{id}/cancel', [LeaveController::class, 'cancel'])->name('cancel');
    // Admin routes
    Route::middleware('permission:manage-leaves')->group(function () {
        Route::get('/admin/all', [LeaveController::class, 'adminIndex'])->name('admin.index');
        Route::post('/{id}/approve', [LeaveController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [LeaveController::class, 'reject'])->name('reject');
    });
});

// Holiday Routes
Route::middleware(['auth'])->prefix('holidays')->name('holidays.')->group(function () {
    Route::get('/', [HolidayController::class, 'index'])->name('index');
    Route::middleware('permission:manage-holidays')->group(function () {
        Route::get('/create', [HolidayController::class, 'create'])->name('create');
        Route::post('/', [HolidayController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [HolidayController::class, 'edit'])->name('edit');
        Route::put('/{id}', [HolidayController::class, 'update'])->name('update');
        Route::delete('/{id}', [HolidayController::class, 'destroy'])->name('destroy');
    });
});
```

---

## Phase 5 – Views

**Directory structure:**
```
resources/views/
├── attendance/
│   ├── index.blade.php          # My attendance list + today's widget
│   ├── admin/
│   │   ├── index.blade.php      # All users attendance table
│   │   └── show.blade.php       # Single user attendance
│   └── edit.blade.php           # Admin edit form
├── leaves/
│   ├── index.blade.php          # My leave list
│   ├── create.blade.php         # Leave request form
│   ├── show.blade.php           # Leave detail
│   └── admin/
│       └── index.blade.php      # All leave requests (admin)
└── holidays/
    ├── index.blade.php          # Holiday calendar view
    ├── create.blade.php         # Add holiday form
    └── edit.blade.php           # Edit holiday form
```

### View Components Description:

**attendance/index.blade.php:**
- Today's attendance card with clock in/out buttons
- Live timer showing current session duration
- Monthly attendance table (date, login, break, logout, hours, status)

**leaves/index.blade.php:**
- Leave balance summary (L remaining, SL remaining)
- Leave history table with status badges
- "Apply Leave" button

**leaves/create.blade.php:**
- Leave type dropdown (L, SL, H, CO)
- Date range picker (from_date, to_date)
- Reason textarea
- Working days auto-calculation

**holidays/index.blade.php:**
- Calendar grid view of the year
- List view with holiday names and dates
- Admin: Add/Edit/Delete buttons

---

## Phase 6 – Sidebar & Navigation

**File to update:** `resources/views/layouts/partials/sidebar.blade.php`

Add "Attendance" menu section with:
```
Attendance
├── My Attendance       (view-attendance)
├── My Leaves           (view-leaves)
├── Holiday Calendar    (view-holidays)
└── [Admin Section]
    ├── All Attendance  (view-all-attendance)
    ├── Leave Requests  (manage-leaves)
    └── Manage Holidays (manage-holidays)
```

---

## Phase 7 – Permissions & Roles

**File to update:** `config/permissions.php` (or wherever permissions are stored)

Seed the following permissions and assign to appropriate roles:

| Permission | Employee | Manager | Admin | Super Admin |
|---|---|---|---|---|
| view-attendance | ✓ | ✓ | ✓ | ✓ |
| mark-attendance | ✓ | ✓ | ✓ | ✓ |
| view-all-attendance | | ✓ | ✓ | ✓ |
| manage-attendance | | | ✓ | ✓ |
| view-leaves | ✓ | ✓ | ✓ | ✓ |
| create-leaves | ✓ | ✓ | ✓ | ✓ |
| manage-leaves | | ✓ | ✓ | ✓ |
| view-holidays | ✓ | ✓ | ✓ | ✓ |
| manage-holidays | | | ✓ | ✓ |

---

## Business Logic

### Hour Calculation Formula

```
Total Work Hours = (logout_time - login_time) - break_duration
Break Duration   = break_end_time - break_start_time

Examples:
- Login: 09:00, Break: 13:00–14:00, Logout: 18:00
- Gross: 9 hrs, Break: 1 hr, Net: 8 hrs

Edge cases:
- If break_end not recorded: break = 0 (warn admin)
- If logout not recorded by EOD: mark as "incomplete"
- Overnight shifts: use date arithmetic
```

### Attendance Status Rules

| Condition | Status |
|---|---|
| Date is in holidays table | `holiday` |
| Leave approved for this date | `on_leave` |
| login_time recorded | `present` |
| login_time NOT recorded, no leave | `absent` |
| < 4 hours worked | `half_day` |

### Leave Balance (Future Enhancement)
- Each employee gets an annual quota (configurable per role)
- L: 12 days/year, SL: 6 days/year
- System tracks used vs remaining

---

## Implementation Order Summary

| Phase | Task | Status |
|---|---|---|
| Phase 1 | Create 3 database migrations & migrate | ⬜ Pending |
| Phase 2 | Create Attendance, Leave, Holiday models | ⬜ Pending |
| Phase 3 | Create AttendanceController, LeaveController, HolidayController | ⬜ Pending |
| Phase 4 | Add routes to web.php | ⬜ Pending |
| Phase 5 | Create all blade views | ⬜ Pending |
| Phase 6 | Update sidebar navigation | ⬜ Pending |
| Phase 7 | Seed permissions, assign to roles | ⬜ Pending |

---

*Last Updated: 2026-03-04*
*Author: Harsukh (via Claude Code)*
