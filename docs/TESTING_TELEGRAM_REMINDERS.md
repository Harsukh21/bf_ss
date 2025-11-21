# Testing Telegram Reminder Functionality

This guide will help you test the Telegram reminder system in live/production.

## Prerequisites Checklist

Before testing, ensure:

- ✅ Telegram bot token is set in `.env`: `TELEGRAM_BOT_TOKEN`
- ✅ Telegram chat ID is set in `.env`: `TELEGRAM_CHAT_ID`
- ✅ Database migration has been run: `php artisan migrate`
- ✅ Config cache is cleared: `php artisan config:clear`

## Step-by-Step Testing Guide

### 1. Test Telegram Connection

First, verify your Telegram bot can send messages:

```bash
php artisan telegram:test
```

**Expected Result:**
- ✅ Configuration check passes
- ✅ Test message sent successfully
- ✅ You receive a test message on Telegram

**If it fails:**
- Check `.env` file for `TELEGRAM_BOT_TOKEN` and `TELEGRAM_CHAT_ID`
- Verify bot token is correct
- Verify chat ID is correct (can be a personal chat or group)
- Check `storage/logs/laravel.log` for errors

### 2. Verify Database Setup

Check if the `event_reminders` table exists:

```bash
php artisan migrate:status | grep event_reminders
```

If not migrated, run:
```bash
php artisan migrate
```

### 3. Create a Test Reminder

#### Option A: Quick Test (1 minute reminder)

1. Go to `/scorecard` page
2. Find an event that has "IN-PLAY" markets
3. Toggle the "Interrupted" switch ON
4. In the modal, set "Remind Me After" to **1 minute** (for quick testing)
5. Fill in market old limits (optional)
6. Click "Save"

#### Option B: Test with Database Query

You can also manually create a test reminder:

```bash
php artisan tinker
```

Then:
```php
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Get an existing event with exEventId
$event = DB::table('events')->where('is_interrupted', true)->first();

if ($event) {
    $reminderTime = Carbon::now()->addMinutes(1); // 1 minute from now
    
    DB::table('event_reminders')->insert([
        'exEventId' => $event->exEventId,
        'reminder_time' => $reminderTime->format('Y-m-d H:i:s'),
        'sent' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Test reminder created for event: {$event->eventName}\n";
    echo "Reminder time: {$reminderTime->format('Y-m-d H:i:s')}\n";
} else {
    echo "No interrupted events found. Please interrupt an event first.\n";
}
```

### 4. Check Pending Reminders

Verify the reminder was created:

```bash
php artisan tinker --execute="
\$reminders = DB::table('event_reminders')
    ->where('sent', false)
    ->orderBy('reminder_time')
    ->get();
echo 'Pending reminders: ' . \$reminders->count() . PHP_EOL;
foreach (\$reminders as \$r) {
    echo \"- Event: {$r->exEventId}, Time: {$r->reminder_time}\" . PHP_EOL;
}
"
```

### 5. Test the Reminder Command

Manually trigger the reminder check (don't wait for the scheduler):

```bash
php artisan reminders:send
```

**Expected Output:**
```
Found X pending reminder(s).
✓ Reminder sent for event: Event Name (exEventId)
Summary:
  Sent: 1
  Failed: 0
```

**Check Telegram:**
- You should receive the reminder message
- Message format should match the configured format

### 6. Verify Reminder Was Sent

Check the database to confirm the reminder was marked as sent:

```bash
php artisan tinker --execute="
\$sent = DB::table('event_reminders')
    ->where('sent', true)
    ->orderBy('sent_at', 'desc')
    ->first();
if (\$sent) {
    echo \"Last sent reminder:\" . PHP_EOL;
    echo \"- Event: {\$sent->exEventId}\" . PHP_EOL;
    echo \"- Sent at: {\$sent->sent_at}\" . PHP_EOL;
    echo \"- Error: \" . (\$sent->error_message ?? 'None') . PHP_EOL;
} else {
    echo 'No reminders sent yet.' . PHP_EOL;
}
"
```

### 7. Test Scheduled Execution (Production)

The reminder command runs automatically every minute via Laravel's scheduler.

**Ensure cron is set up:**

Add to crontab (`crontab -e`):
```bash
* * * * * cd /var/www/laravel/bf_ss && php artisan schedule:run >> /dev/null 2>&1
```

**Test the scheduler:**
```bash
php artisan schedule:list
```

You should see:
```
reminders:send  ... Every minute
```

**Verify scheduler is running:**
```bash
# Check if schedule:run is being executed
# This should be called by cron every minute
php artisan schedule:run
```

## Troubleshooting

### Reminder Not Sending

1. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verify reminder exists and time has passed:**
   ```sql
   SELECT * FROM event_reminders 
   WHERE sent = false 
   AND reminder_time <= NOW()
   ORDER BY reminder_time;
   ```

3. **Manually test the command:**
   ```bash
   php artisan reminders:send -v
   ```

### Telegram Not Receiving Messages

1. **Test Telegram connection:**
   ```bash
   php artisan telegram:test
   ```

2. **Check bot token and chat ID:**
   ```bash
   php artisan tinker --execute="
   echo 'Bot Token: ' . (config('services.telegram.bot_token') ? 'SET' : 'NOT SET') . PHP_EOL;
   echo 'Chat ID: ' . (config('services.telegram.chat_id') ?: 'NOT SET') . PHP_EOL;
   "
   ```

3. **Verify bot can send messages:**
   - Send a message to your bot first
   - Or add bot to a group and send a message there

### Scheduler Not Running

1. **Check cron is active:**
   ```bash
   crontab -l
   ```

2. **Test schedule manually:**
   ```bash
   php artisan schedule:run
   ```

3. **Check system logs for cron errors**

## Complete Test Flow

Here's a complete test flow for production:

1. **Setup (one-time):**
   ```bash
   php artisan migrate
   php artisan config:clear
   php artisan telegram:test
   ```

2. **Create Test Reminder:**
   - Go to `/scorecard`
   - Interrupt an event
   - Set "Remind Me After" to 1 minute
   - Save

3. **Wait 1 minute OR manually trigger:**
   ```bash
   php artisan reminders:send
   ```

4. **Verify:**
   - Check Telegram for message
   - Check database: `SELECT * FROM event_reminders WHERE sent = true ORDER BY sent_at DESC LIMIT 1;`

5. **Production Setup:**
   - Add cron job: `* * * * * cd /var/www/laravel/bf_ss && php artisan schedule:run >> /dev/null 2>&1`
   - Verify: `php artisan schedule:list`

## Message Format

The reminder message format is:
```
🔔 Limit Reminder

Event: [Event Name]
Sport: [Sport Name]

Market Old Limits:
  • [Market Name]: [Old Limit]
  • [Market Name]: [Old Limit]

Reminder set for [X] minutes.
I have set a 0–1 limit in the market for the event mentioned above.
```

## Support

If you encounter issues:
1. Check `storage/logs/laravel.log`
2. Verify all configuration is correct
3. Test Telegram connection separately
4. Check database for reminder records

