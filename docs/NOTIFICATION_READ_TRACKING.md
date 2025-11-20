# Notification Read Tracking Guide

## How to Track if User Entered PIN and Read Notification

The notification system tracks when a user enters their Web PIN and reads a notification through the `notification_user` pivot table.

### Database Tracking

When a notification is created and assigned to users, a record is created in the `notification_user` pivot table with:
- `is_read` (boolean) - Default: `false`
- `read_at` (timestamp) - Default: `null`
- `is_delivered` (boolean) - Default: `false`
- `delivered_at` (timestamp) - Default: `null`
- `delivery_status` (JSON) - Tracks delivery status for each method (push, telegram, login_popup)

### PIN Verification Flow

1. **User Logs In**: System loads pending notifications for the user
2. **Popup Appears**: If notification has `requires_web_pin = true`, user must enter PIN to close
3. **PIN Verification**: When user clicks "Verify PIN":
   - PIN is sent to `/notifications/{id}/mark-read` endpoint
   - Controller verifies PIN against user's `web_pin` field
   - If correct: Updates `is_read = true` and `read_at = timestamp` in pivot table
   - If incorrect: Returns error message

### Checking Read Status

#### In Notification Listing
The notifications listing (`/notifications`) shows:
- **Read Count**: Number of users who have read the notification
- **Unread Count**: Number of users who haven't read it yet

#### Via Database Query

```php
// Get all users who have read a specific notification
$readUsers = $notification->users()
    ->wherePivot('is_read', true)
    ->get();

// Get all users who haven't read a specific notification
$unreadUsers = $notification->users()
    ->wherePivot('is_read', false)
    ->get();

// Check if specific user has read a notification
$userHasRead = $notification->users()
    ->wherePivot('user_id', $userId)
    ->wherePivot('is_read', true)
    ->exists();

// Get read timestamp for a specific user
$readAt = $notification->users()
    ->wherePivot('user_id', $userId)
    ->wherePivot('is_read', true)
    ->value('read_at');
```

#### Via API

```javascript
// Get pending notifications for current user
GET /notifications/pending

// Mark notification as read (requires PIN verification)
POST /notifications/{id}/mark-read
{
    "web_pin": "123456"
}
```

### Notification Status Fields

- `is_read`: `true` = User entered correct PIN and read notification
- `read_at`: Timestamp when user successfully verified PIN and read notification
- `is_delivered`: `true` = Notification was delivered via any method
- `delivered_at`: Timestamp when notification was delivered
- `delivery_status`: JSON object tracking delivery status:
  ```json
  {
    "push": true/false,
    "telegram": true/false,
    "login_popup": true/false
  }
  ```

### Example: Check if User Read Notification

```php
$notification = Notification::find($id);
$user = User::find($userId);

// Check if user has read this notification
$hasRead = $notification->users()
    ->wherePivot('user_id', $user->id)
    ->wherePivot('is_read', true)
    ->exists();

if ($hasRead) {
    // Get when they read it
    $readAt = $notification->users()
        ->wherePivot('user_id', $user->id)
        ->wherePivot('is_read', true)
        ->value('pivot.read_at');
    
    echo "User read notification on: " . $readAt;
} else {
    echo "User has not read this notification yet";
}
```

### How to Know if User Read Notification by Entering Web PIN

**Simple Answer:** Check the `is_read` and `read_at` fields in the `notification_user` pivot table.

**Detailed Process:**

1. **When Notification is Created**: Each user assigned to the notification gets a record in `notification_user` table:
   - `is_read = false` (not read yet)
   - `read_at = null` (no read timestamp)

2. **When User Enters Correct Web PIN**: 
   - The system verifies the PIN in `NotificationController@markAsRead`
   - If PIN is correct, it updates:
     - `is_read = true`
     - `read_at = current timestamp`
   - If PIN is incorrect, fields remain unchanged

3. **To Check if User Read Notification:**

```php
// Method 1: Check if specific user read specific notification
$notification = Notification::find($notificationId);
$user = User::find($userId);

$pivotRecord = DB::table('notification_user')
    ->where('notification_id', $notificationId)
    ->where('user_id', $userId)
    ->first();

if ($pivotRecord && $pivotRecord->is_read === true) {
    echo "User read the notification at: " . $pivotRecord->read_at;
} else {
    echo "User has NOT read the notification yet";
}

// Method 2: Using Eloquent Relationship
$hasRead = $notification->users()
    ->wherePivot('user_id', $userId)
    ->wherePivot('is_read', true)
    ->exists();

if ($hasRead) {
    $readAt = $notification->users()
        ->wherePivot('user_id', $userId)
        ->value('pivot.read_at');
    echo "User read at: " . $readAt;
}

// Method 3: Get all users who read a notification
$readUsers = DB::table('notification_user')
    ->where('notification_id', $notificationId)
    ->where('is_read', true)
    ->join('users', 'notification_user.user_id', '=', 'users.id')
    ->select('users.name', 'users.email', 'notification_user.read_at')
    ->get();

foreach ($readUsers as $user) {
    echo $user->name . " read at: " . $user->read_at . "\n";
}
```

4. **In the Notification Listing Page** (`/notifications`):
   - The page automatically shows "X Read" and "Y Unread" badges
   - This uses the same `is_read` field to count

### Important Notes

- **PIN Required**: If `requires_web_pin = true`, user MUST enter correct PIN to close popup
- **Automatic Tracking**: Read status is automatically updated when PIN is verified correctly
- **No PIN = No Read**: If user doesn't enter PIN (or enters wrong PIN), `is_read` remains `false` and `read_at` remains `null`
- **Recurring Notifications**: For daily/weekly/monthly notifications, `is_read` is reset when notification is resent
- **Direct Query**: You can directly query `notification_user` table without Eloquent:
  ```sql
  SELECT * FROM notification_user 
  WHERE notification_id = ? AND user_id = ? AND is_read = true;
  ```

