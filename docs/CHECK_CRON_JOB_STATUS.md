# How to Check if Cron Job is Working on Live Server

This guide shows multiple ways to verify if your Laravel scheduler cron job is running correctly on your live AWS server.

## Method 1: Check Scheduler Logs (Easiest & Recommended)

If you enabled logging in your crontab:

```bash
# SSH into your server
ssh -i your-key.pem ec2-user@your-server-ip

# Navigate to your project
cd /var/www/laravel/bf_ss

# View scheduler logs (real-time)
tail -f storage/logs/scheduler.log

# View last 50 lines
tail -n 50 storage/logs/scheduler.log

# Search for recent activity
grep "$(date +\%Y-\%m-\%d)" storage/logs/scheduler.log

# Check if scheduler ran in last 5 minutes
grep "$(date +\%Y-\%m-\%d\ \%H:\%M)" storage/logs/scheduler.log
```

**Expected Output (if working):**
```
Running scheduled command: reminders:send
No pending reminders to send.
```

## Method 2: Check Laravel Logs

```bash
# View Laravel application logs
tail -f storage/logs/laravel.log

# Search for reminder-related entries
grep "reminders:send" storage/logs/laravel.log

# View recent entries
tail -n 100 storage/logs/laravel.log | grep -i reminder
```

## Method 3: Check Cron Service Logs

### For Ubuntu/Debian:
```bash
# Check cron service status
sudo systemctl status cron

# View cron execution logs
sudo tail -f /var/log/syslog | grep CRON

# Or check auth log
sudo tail -f /var/log/auth.log | grep CRON
```

### For Amazon Linux/CentOS/RHEL:
```bash
# Check cron service status
sudo systemctl status crond

# View cron execution logs
sudo tail -f /var/log/cron

# View messages log
sudo tail -f /var/log/messages | grep CRON
```

**Expected Output (if cron is running):**
```
Nov 20 10:45:01 server-name CRON[12345]: (ec2-user) CMD (cd /var/www/laravel/bf_ss && php artisan schedule:run >> /dev/null 2>&1)
```

## Method 4: Check if Cron Job Exists

```bash
# View current crontab entries
crontab -l

# Check if Laravel scheduler is in crontab
crontab -l | grep "schedule:run"

# Check for specific user's crontab (if using different user)
sudo crontab -u www-data -l
# or
sudo crontab -u ec2-user -l
```

**Expected Output:**
```
* * * * * cd /var/www/laravel/bf_ss && php artisan schedule:run >> /dev/null 2>&1
```

## Method 5: Test Command Manually

```bash
# Navigate to project directory
cd /var/www/laravel/bf_ss

# Run the scheduler command manually
php artisan schedule:run

# Run the reminder command directly
php artisan reminders:send

# Check what commands are scheduled
php artisan schedule:list
```

**Expected Output from `schedule:list`:**
```
Next Due: 2025-11-20 10:46:00
Command: reminders:send
```

## Method 6: Check Database for Reminder Activity

```bash
# Access Laravel tinker
php artisan tinker
```

Then run these commands:

```php
// Check pending reminders
DB::table('event_reminders')->where('sent', false)->get();

// Check recently sent reminders
DB::table('event_reminders')
    ->where('sent', true)
    ->orderBy('sent_at', 'desc')
    ->take(5)
    ->get();

// Count reminders by status
DB::table('event_reminders')
    ->select('sent', DB::raw('count(*) as count'))
    ->groupBy('sent')
    ->get();

// Check reminders scheduled for today
DB::table('event_reminders')
    ->whereDate('reminder_time', today())
    ->get();
```

## Method 7: Create a Test Reminder

1. **Go to Scorecard page:** `/scorecard`
2. **Toggle an event's "Interrupted" switch ON**
3. **Set "Remind Me After" to 1 minute**
4. **Fill in old limits (required)**
5. **Submit the form**
6. **Wait 1-2 minutes**
7. **Check Telegram for the message**
8. **Check database:**

```bash
php artisan tinker
```

```php
// Check if reminder was created
DB::table('event_reminders')
    ->where('sent', false)
    ->where('reminder_time', '<=', now())
    ->get();

// Check after 2 minutes if it was sent
DB::table('event_reminders')
    ->where('sent', true)
    ->orderBy('sent_at', 'desc')
    ->first();
```

## Method 8: Monitor in Real-Time

Create a monitoring script:

```bash
# Create monitoring script
cat > /tmp/monitor-cron.sh << 'EOF'
#!/bin/bash
while true; do
    echo "=== $(date) ==="
    tail -n 1 /var/www/laravel/bf_ss/storage/logs/scheduler.log 2>/dev/null || echo "No scheduler log found"
    echo ""
    sleep 60
done
EOF

chmod +x /tmp/monitor-cron.sh

# Run monitoring (will show updates every minute)
/tmp/monitor-cron.sh
```

## Method 9: Check Process Running

```bash
# Check if cron processes are running
ps aux | grep cron

# Check if schedule:run process exists
ps aux | grep "schedule:run"

# Check if PHP processes are running (cron might trigger PHP)
ps aux | grep "php artisan"
```

## Method 10: Verify Cron Configuration

```bash
# Check cron service is running
sudo systemctl status cron
# or
sudo systemctl status crond

# If not running, start it
sudo systemctl start cron
# or
sudo systemctl start crond

# Enable cron to start on boot
sudo systemctl enable cron
# or
sudo systemctl enable crond
```

## Troubleshooting Checklist

### ✅ Cron Job is NOT Running?

1. **Check cron service:**
   ```bash
   sudo systemctl status cron
   ```

2. **Check crontab syntax:**
   ```bash
   crontab -l
   # Verify path is correct
   # Verify PHP path is correct
   ```

3. **Check file permissions:**
   ```bash
   ls -la /var/www/laravel/bf_ss/artisan
   # Should be executable: -rwxr-xr-x
   ```

4. **Check PHP path:**
   ```bash
   which php
   # Use full path in crontab: /usr/bin/php
   ```

5. **Test command manually:**
   ```bash
   cd /var/www/laravel/bf_ss
   php artisan schedule:run
   ```

6. **Check Laravel logs for errors:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### ✅ Cron Job is Running but Not Working?

1. **Check if reminders exist:**
   ```bash
   php artisan tinker
   DB::table('event_reminders')->where('sent', false)->count();
   ```

2. **Check Telegram configuration:**
   ```bash
   php artisan config:show services.telegram
   # Or check .env file
   grep TELEGRAM .env
   ```

3. **Test Telegram connection:**
   ```bash
   php artisan telegram:test
   ```

4. **Run reminder command with verbose output:**
   ```bash
   php artisan reminders:send -v
   ```

## Quick Verification Script

Create this script to check everything at once:

```bash
cat > /tmp/check-cron-status.sh << 'EOF'
#!/bin/bash

echo "=========================================="
echo "Cron Job Status Check"
echo "=========================================="
echo ""

# Check cron service
echo "1. Cron Service Status:"
if systemctl is-active --quiet cron || systemctl is-active --quiet crond; then
    echo "   ✓ Cron service is running"
else
    echo "   ✗ Cron service is NOT running"
fi
echo ""

# Check crontab
echo "2. Crontab Entries:"
if crontab -l 2>/dev/null | grep -q "schedule:run"; then
    echo "   ✓ Laravel scheduler found in crontab"
    crontab -l 2>/dev/null | grep "schedule:run"
else
    echo "   ✗ Laravel scheduler NOT found in crontab"
fi
echo ""

# Check scheduler log
echo "3. Recent Scheduler Activity:"
if [ -f "/var/www/laravel/bf_ss/storage/logs/scheduler.log" ]; then
    echo "   Recent entries:"
    tail -n 5 /var/www/laravel/bf_ss/storage/logs/scheduler.log | sed 's/^/   /'
else
    echo "   ⚠ Scheduler log file not found"
fi
echo ""

# Check last run time
echo "4. Last Scheduler Run:"
LAST_RUN=$(grep "$(date +\%Y-\%m-\%d)" /var/www/laravel/bf_ss/storage/logs/scheduler.log 2>/dev/null | tail -1 | awk '{print $1, $2}')
if [ -n "$LAST_RUN" ]; then
    echo "   Last run: $LAST_RUN"
else
    echo "   ⚠ No runs found for today"
fi
echo ""

# Check pending reminders
echo "5. Pending Reminders in Database:"
cd /var/www/laravel/bf_ss
PENDING=$(php artisan tinker --execute="echo DB::table('event_reminders')->where('sent', false)->count();" 2>/dev/null)
echo "   Pending reminders: $PENDING"
echo ""

# Test command
echo "6. Test Scheduler Command:"
cd /var/www/laravel/bf_ss
php artisan schedule:run 2>&1 | sed 's/^/   /'
echo ""

echo "=========================================="
EOF

chmod +x /tmp/check-cron-status.sh

# Run the check
/tmp/check-cron-status.sh
```

## Expected Behavior

### ✅ Working Correctly:

1. **Cron service is running**
2. **Crontab has Laravel scheduler entry**
3. **Scheduler.log shows activity every minute:**
   ```
   Running scheduled command: reminders:send
   No pending reminders to send.
   ```
4. **When reminders exist, they are sent:**
   ```
   Found 1 pending reminder(s).
   ✓ Reminder sent for event: Event Name
   ```
5. **Telegram messages are received**

### ❌ Not Working:

1. **Cron service is stopped**
2. **No crontab entry for Laravel scheduler**
3. **Scheduler.log doesn't exist or has no entries**
4. **Reminders stay in pending state**
5. **No Telegram messages received**

## Quick Test Command

Run this to quickly test everything:

```bash
cd /var/www/laravel/bf_ss && \
echo "=== Testing Cron Setup ===" && \
echo "1. Testing scheduler:" && \
php artisan schedule:run && \
echo "" && \
echo "2. Testing reminder command:" && \
php artisan reminders:send && \
echo "" && \
echo "3. Checking pending reminders:" && \
php artisan tinker --execute="echo 'Pending: ' . DB::table('event_reminders')->where('sent', false)->count();" && \
echo "" && \
echo "=== Test Complete ==="
```

## Still Not Working?

1. **Check file permissions:**
   ```bash
   sudo chown -R www-data:www-data /var/www/laravel/bf_ss/storage
   sudo chmod -R 775 /var/www/laravel/bf_ss/storage
   ```

2. **Check PHP permissions:**
   ```bash
   which php
   php -v
   ```

3. **Clear Laravel caches:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

4. **Check system time:**
   ```bash
   date
   timedatectl
   ```

5. **Review full error logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

