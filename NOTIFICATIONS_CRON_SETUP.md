# Notifications Cron Setup Guide for Production

This guide explains how to set up the cron job for scheduled notifications and event reminders in your live/production environment.

## Overview

Your application has two scheduled commands that need to run via Laravel's scheduler:

1. **`reminders:send`** - Sends Telegram reminders for interrupted events (runs every minute)
2. **`notifications:send-scheduled`** - Sends scheduled notifications (daily, weekly, monthly) (runs every minute)

Both commands are already configured in `bootstrap/app.php` to run every minute via Laravel's task scheduler.

## Prerequisites

- SSH access to your production server
- Cron service enabled on your server
- Laravel application deployed and running
- PHP CLI available in your server's PATH

## Step 1: Find Your Application Path

First, you need to know the absolute path to your Laravel application on the server:

```bash
# Navigate to your application directory
cd /var/www/laravel/bf_ss  # Or your actual application path

# Get the full path
pwd
```

**Note:** Replace `/var/www/laravel/bf_ss` with your actual application path in the following steps.

## Step 2: Find PHP CLI Path

Find the path to your PHP CLI executable:

```bash
which php
# Output example: /usr/bin/php or /opt/php/bin/php
```

Or test with:
```bash
php -v
```

**Note:** You may need to use the full path to PHP instead of just `php` if it's not in your PATH.

## Step 3: Set Up the Cron Job

### Option A: Using crontab (Recommended)

1. **Open the crontab editor for your web server user** (usually `www-data`, `nginx`, `apache`, or your specific user):

```bash
# For www-data user (common for Apache/Nginx)
sudo crontab -u www-data -e

# Or if you're running as a specific user:
crontab -e
```

2. **Add the following line to the crontab:**

```bash
* * * * * cd /var/www/laravel/bf_ss && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**Important:** Replace:
- `/var/www/laravel/bf_ss` with your actual application path
- `/usr/bin/php` with your actual PHP CLI path (from Step 2)

### Option B: Add to Server-Wide Crontab

If you prefer to add it to the system crontab:

```bash
sudo crontab -e
```

Then add the same line as above, but make sure to specify the correct user:

```bash
* * * * * cd /var/www/laravel/bf_ss && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

## Step 4: Verify the Cron Job

### Check if cron job was added:

```bash
# For www-data user
sudo crontab -u www-data -l

# Or for current user
crontab -l
```

You should see the line you just added.

### Test the scheduler manually:

```bash
cd /var/www/laravel/bf_ss
php artisan schedule:run
```

This will show you which scheduled tasks are due and execute them. You should see output like:
```
Running scheduled command: reminders:send
Running scheduled command: notifications:send-scheduled
```

## Step 5: Monitor Cron Execution

### Check Laravel Logs

Monitor your Laravel logs to see if the scheduled commands are running:

```bash
tail -f storage/logs/laravel.log
```

### Check Cron Logs

Some servers log cron job execution. Check:

```bash
# Common cron log locations
tail -f /var/log/cron
tail -f /var/log/syslog | grep CRON
```

### Test Commands Individually

You can test each command manually to ensure they work:

```bash
# Test reminders command
php artisan reminders:send

# Test notifications command
php artisan notifications:send-scheduled
```

## Troubleshooting

### Issue: Cron job not running

**Solution 1:** Check if cron service is running:
```bash
sudo systemctl status cron
# or
sudo service cron status
```

If not running, start it:
```bash
sudo systemctl start cron
# or
sudo service cron start
```

**Solution 2:** Verify file permissions:
```bash
# Ensure the artisan file is executable
chmod +x /var/www/laravel/bf_ss/artisan

# Ensure storage/logs directory is writable
chmod -R 775 /var/www/laravel/bf_ss/storage
chmod -R 775 /var/www/laravel/bf_ss/bootstrap/cache
```

**Solution 3:** Check if PHP path is correct:
```bash
# Try running the command manually
/usr/bin/php artisan schedule:run

# If it fails, try with full path
/usr/bin/php /var/www/laravel/bf_ss/artisan schedule:run
```

### Issue: Commands running but notifications not sending

1. **Check queue connection:**
   - Ensure your `.env` has `QUEUE_CONNECTION=database` (or your preferred queue driver)
   - If using database queue, ensure queue worker is running: `php artisan queue:work`

2. **Check Telegram credentials:**
   - Verify `TELEGRAM_BOT_TOKEN` is set in `.env`
   - Verify `TELEGRAM_CHAT_ID` (if using default chat) is set in `.env`

3. **Check notification status:**
   ```bash
   # Check pending notifications
   php artisan tinker
   >>> DB::table('notifications')->where('status', 'pending')->get();
   ```

### Issue: Permission denied errors

Make sure the cron user has proper permissions:

```bash
# Set ownership (adjust user/group as needed)
sudo chown -R www-data:www-data /var/www/laravel/bf_ss

# Or set permissions
sudo chmod -R 755 /var/www/laravel/bf_ss
sudo chmod -R 775 /var/www/laravel/bf_ss/storage
sudo chmod -R 775 /var/www/laravel/bf_ss/bootstrap/cache
```

## Cron Syntax Explained

The cron expression `* * * * *` means:
- `*` - Every minute
- `*` - Every hour
- `*` - Every day of month
- `*` - Every month
- `*` - Every day of week

So `* * * * *` runs the command every minute.

## Alternative: Using Supervisor (For Queue Workers)

If you're using Laravel queues, you might want to set up Supervisor to manage queue workers:

1. Install Supervisor (if not installed):
```bash
sudo apt-get install supervisor  # Debian/Ubuntu
sudo yum install supervisor      # CentOS/RHEL
```

2. Create a supervisor configuration file:
```bash
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

3. Add configuration:
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/laravel/bf_ss/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/laravel/bf_ss/storage/logs/worker.log
stopwaitsecs=3600
```

4. Reload Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## Summary

After completing these steps, your scheduled notifications and reminders will run automatically every minute. The Laravel scheduler will:

1. Check which scheduled tasks are due
2. Run `reminders:send` every minute (if there are pending reminders)
3. Run `notifications:send-scheduled` every minute (if there are scheduled notifications due)

## Quick Setup Command (One-Liner)

Replace the paths and run this command to quickly set up the cron job:

```bash
# For www-data user
(sudo crontab -u www-data -l 2>/dev/null; echo "* * * * * cd /var/www/laravel/bf_ss && /usr/bin/php artisan schedule:run >> /dev/null 2>&1") | sudo crontab -u www-data -

# Or for current user
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/laravel/bf_ss && /usr/bin/php artisan schedule:run >> /dev/null 2>&1") | crontab -
```

**Remember to replace:**
- `/var/www/laravel/bf_ss` with your actual application path
- `/usr/bin/php` with your actual PHP CLI path

## Additional Resources

- [Laravel Task Scheduling Documentation](https://laravel.com/docs/scheduling)
- [Linux Cron Guide](https://www.ostechnix.com/a-beginners-guide-to-cron-jobs/)

