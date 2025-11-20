# How to Test Telegram Bot Messages

This guide shows you how to test all Telegram bot message functionality, including immediate interruption notifications and scheduled reminders.

## Quick Test Commands

### 1. Test Basic Telegram Connection

Test if your Telegram bot can send messages:

```bash
php artisan telegram:test
```

**Expected Output:**
```
✅ Bot Token: 1234567890...
✅ Chat ID: -1001234567890
Sending test message...
✅ Test message sent successfully!
Check your Telegram chat to verify.
```

**If you receive the test message in Telegram, your bot is working correctly! ✅**

---

## Testing Immediate Interruption Notification

When you toggle "Interrupted" ON for the first time and submit the form, an immediate Telegram notification should be sent.

### Step 1: Prepare Test Event

1. Go to `/scorecard` page
2. Find an event that has "IN-PLAY" markets
3. Make sure the event's "Interrupted" toggle is **OFF** (not already interrupted)

### Step 2: Create Interruption

1. **Toggle "Interrupted" switch ON** (it will open the modal)
2. **Fill in the form:**
   - Enter old limits for markets (e.g., Moneyline: 5)
   - Select "Remind Me After" time (e.g., 5 min)
   - Click "Save"

### Step 3: Check Telegram

**Immediately after clicking "Save", check your Telegram:**
- You should receive a message with:
  - ⚠️ **Event Interrupted** header
  - Event name and sport
  - Market old limits
  - Reminder schedule info
  - The standard limit message

**Example Message:**
```
⚠️ Event Interrupted

Event: Phoenix Suns @ Portland Trail Blazers
Sport: Basketball

Market Old Limits:
  • Moneyline: 5

Reminder scheduled for 5 minutes.

I have set a 0–1 limit in the market for the event mentioned above.
```

### Step 4: Verify in Database

Check if the interruption was recorded:

```bash
php artisan tinker --execute="
\$event = DB::table('events')->where('is_interrupted', true)->orderBy('updated_at', 'desc')->first();
if (\$event) {
    echo 'Event: ' . \$event->eventName . PHP_EOL;
    echo 'Interrupted: ' . (\$event->is_interrupted ? 'Yes' : 'No') . PHP_EOL;
    echo 'Remind Me After: ' . (\$event->remind_me_after ?? 'Not set') . PHP_EOL;
} else {
    echo 'No interrupted events found.' . PHP_EOL;
}
"
```

---

## Testing Scheduled Reminder Messages

The reminder messages are sent automatically after the specified time (e.g., 5 minutes).

### Method 1: Wait for Scheduled Time

1. Create an interruption (as above)
2. Set "Remind Me After" to **5 minutes**
3. **Wait 5 minutes** (or the time you specified)
4. The scheduler will automatically send the reminder
5. Check Telegram for the reminder message

### Method 2: Test Immediately (Manual Trigger)

Instead of waiting, manually trigger the reminder command:

```bash
php artisan reminders:send
```

**This will send ALL pending reminders that are due (reminder_time <= now).**

### Method 3: Create Test Reminder for Immediate Testing

Create a reminder that's due right now:

```bash
php artisan tinker
```

```php
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Get an interrupted event
$event = DB::table('events')
    ->where('is_interrupted', true)
    ->first();

if ($event) {
    // Create reminder that's due NOW (or 1 second ago)
    $reminderTime = Carbon::now()->subSecond();
    
    DB::table('event_reminders')->updateOrInsert(
        [
            'exEventId' => $event->exEventId,
            'reminder_time' => $reminderTime->format('Y-m-d H:i:s'),
        ],
        [
            'reminder_time' => $reminderTime->format('Y-m-d H:i:s'),
            'sent' => false,
            'sent_at' => null,
            'error_message' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );
    
    echo "✅ Test reminder created (due immediately)\n";
    echo "Event: {$event->eventName}\n";
    echo "Reminder time: {$reminderTime->format('Y-m-d H:i:s')}\n";
    echo "\nNow run: php artisan reminders:send\n";
} else {
    echo "❌ No interrupted events found. Create an interruption first.\n";
}
```

Then run:
```bash
php artisan reminders:send
```

---

## Complete Testing Workflow

### Test 1: Basic Connection

```bash
# Step 1: Test Telegram connection
php artisan telegram:test

# Expected: ✅ Test message received in Telegram
```

### Test 2: Immediate Interruption Notification

```bash
# Step 1: Go to /scorecard page
# Step 2: Toggle "Interrupted" ON for an event (first time)
# Step 3: Fill form (old limits + remind me after)
# Step 4: Click Save
# Step 5: Check Telegram immediately

# Expected: ⚠️ "Event Interrupted" message received
```

### Test 3: Scheduled Reminder

```bash
# Step 1: Create interruption with "Remind Me After: 1 minute"
# Step 2: Wait 1 minute OR run:
php artisan reminders:send

# Expected: 🔔 "Limit Reminder" message received after time passes
```

---

## Check Pending Reminders

See what reminders are scheduled:

```bash
php artisan tinker --execute="
\$reminders = DB::table('event_reminders')
    ->where('sent', false)
    ->orderBy('reminder_time')
    ->get();
    
echo 'Pending Reminders: ' . \$reminders->count() . PHP_EOL . PHP_EOL;

foreach (\$reminders as \$r) {
    \$event = DB::table('events')->where('exEventId', \$r->exEventId)->first();
    \$eventName = \$event ? \$event->eventName : 'Unknown';
    \$timeUntil = now()->diffInMinutes(Carbon\Carbon::parse(\$r->reminder_time));
    
    echo \"Event: {$eventName}\" . PHP_EOL;
    echo \"Reminder Time: {$r->reminder_time}\" . PHP_EOL;
    echo \"Time Until: {$timeUntil} minutes\" . PHP_EOL;
    echo \"---\" . PHP_EOL;
}
"
```

---

## Check Sent Reminders

See which reminders were already sent:

```bash
php artisan tinker --execute="
\$sent = DB::table('event_reminders')
    ->where('sent', true)
    ->orderBy('sent_at', 'desc')
    ->limit(5)
    ->get();
    
echo 'Recently Sent Reminders: ' . \$sent->count() . PHP_EOL . PHP_EOL;

foreach (\$sent as \$r) {
    \$event = DB::table('events')->where('exEventId', \$r->exEventId)->first();
    \$eventName = \$event ? \$event->eventName : 'Unknown';
    
    echo \"Event: {$eventName}\" . PHP_EOL;
    echo \"Sent At: {$r->sent_at}\" . PHP_EOL;
    echo \"Error: \" . (\$r->error_message ?? 'None') . PHP_EOL;
    echo \"---\" . PHP_EOL;
}
"
```

---

## Test Both Messages in Sequence

Test the complete flow:

1. **Go to `/scorecard`**
2. **Find an event with "Interrupted" toggle OFF**
3. **Toggle it ON (opens modal)**
4. **Fill form:**
   - Old limit: 5
   - Remind Me After: 2 minutes
   - Click "Save"
5. **Check Telegram immediately:** You should get ⚠️ "Event Interrupted" message
6. **Wait 2 minutes OR run manually:**
   ```bash
   php artisan reminders:send
   ```
7. **Check Telegram again:** You should get 🔔 "Limit Reminder" message

---

## Troubleshooting

### No Telegram Messages Received

1. **Test basic connection first:**
   ```bash
   php artisan telegram:test
   ```

2. **Check configuration:**
   ```bash
   php artisan tinker --execute="
   echo 'Bot Token: ' . (config('services.telegram.bot_token') ? 'SET ✅' : 'NOT SET ❌') . PHP_EOL;
   echo 'Chat ID: ' . (config('services.telegram.chat_id') ?: 'NOT SET ❌') . PHP_EOL;
   "
   ```

3. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log | grep -i telegram
   ```

4. **Clear config cache:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

### Immediate Notification Not Sending

1. **Check if event was already interrupted:**
   ```bash
   php artisan tinker --execute="
   \$event = DB::table('events')->where('exEventId', 'YOUR_EX_EVENT_ID')->first();
   echo 'Was interrupted before: ' . (\$event->is_interrupted ? 'Yes' : 'No') . PHP_EOL;
   "
   ```
   **Note:** Immediate notification only sends on FIRST interruption.

2. **Check logs for errors:**
   ```bash
   tail -f storage/logs/laravel.log | grep -i "interruption notification"
   ```

### Reminder Not Sending

1. **Check if reminder exists:**
   ```bash
   php artisan tinker --execute="
   \$pending = DB::table('event_reminders')
       ->where('sent', false)
       ->where('reminder_time', '<=', now())
       ->count();
   echo 'Pending reminders due: ' . \$pending . PHP_EOL;
   "
   ```

2. **Run command manually:**
   ```bash
   php artisan reminders:send -v
   ```

3. **Check if scheduler is running:**
   ```bash
   php artisan schedule:list
   crontab -l | grep schedule:run
   ```

---

## Quick Test Script

Create this script to test everything at once:

```bash
cat > /tmp/test-telegram-all.sh << 'EOF'
#!/bin/bash

echo "=========================================="
echo "Telegram Bot Testing"
echo "=========================================="
echo ""

# Test 1: Basic connection
echo "1. Testing basic Telegram connection..."
php artisan telegram:test
echo ""

# Test 2: Check configuration
echo "2. Checking configuration..."
php artisan tinker --execute="
echo 'Bot Token: ' . (config('services.telegram.bot_token') ? 'SET ✅' : 'NOT SET ❌') . PHP_EOL;
echo 'Chat ID: ' . (config('services.telegram.chat_id') ?: 'NOT SET ❌') . PHP_EOL;
"
echo ""

# Test 3: Check pending reminders
echo "3. Checking pending reminders..."
php artisan tinker --execute="
\$pending = DB::table('event_reminders')->where('sent', false)->count();
echo 'Pending reminders: ' . \$pending . PHP_EOL;
"
echo ""

# Test 4: Check interrupted events
echo "4. Checking interrupted events..."
php artisan tinker --execute="
\$interrupted = DB::table('events')->where('is_interrupted', true)->count();
echo 'Interrupted events: ' . \$interrupted . PHP_EOL;
"
echo ""

# Test 5: Manually trigger reminders
echo "5. Manually triggering reminder command..."
php artisan reminders:send
echo ""

echo "=========================================="
echo "Test Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Go to /scorecard page"
echo "2. Toggle 'Interrupted' ON for an event"
echo "3. Fill form and save"
echo "4. Check Telegram for immediate notification"
echo "5. Wait for scheduled time OR run: php artisan reminders:send"
EOF

chmod +x /tmp/test-telegram-all.sh

# Run the test
/tmp/test-telegram-all.sh
```

---

## Message Formats

### Immediate Interruption Notification:
```
⚠️ Event Interrupted

Event: [Event Name]
Sport: [Sport Name]

Market Old Limits:
  • [Market Name]: [Old Limit]

Reminder scheduled for [X] minutes.

I have set a 0–1 limit in the market for the event mentioned above.
```

### Scheduled Reminder Message:
```
🔔 Limit Reminder

Event: [Event Name]
Sport: [Sport Name]

Market Old Limits:
  • [Market Name]: [Old Limit]

Reminder set for [X] minutes.
I have set a 0–1 limit in the market for the event mentioned above.
```

---

## Summary

✅ **Basic Connection Test:** `php artisan telegram:test`

✅ **Test Immediate Notification:**
- Toggle "Interrupted" ON (first time)
- Fill form and save
- Check Telegram immediately

✅ **Test Scheduled Reminder:**
- Create interruption with "Remind Me After"
- Wait for time OR run: `php artisan reminders:send`
- Check Telegram

✅ **Verify in Database:**
- Check `events` table for `is_interrupted = true`
- Check `event_reminders` table for scheduled reminders

