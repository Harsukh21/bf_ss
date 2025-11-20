# AWS Telegram Scheduler Setup Guide

This guide explains how to set up the Telegram reminder scheduler on your AWS server.

## Prerequisites

✅ Laravel scheduler is already configured in `bootstrap/app.php`  
✅ Command `reminders:send` is ready  
✅ Telegram bot token and chat ID configured in `.env`

## Option 1: AWS EC2 (Linux Server) - Recommended

If you're running your Laravel application on an EC2 instance (Ubuntu/Amazon Linux/CentOS):

### Step 1: SSH into Your EC2 Instance

```bash
ssh -i your-key.pem ec2-user@your-ec2-ip
# or
ssh -i your-key.pem ubuntu@your-ec2-ip
```

### Step 2: Navigate to Your Project Directory

```bash
cd /var/www/laravel/bf_ss
# or wherever your Laravel project is located
```

### Step 3: Test the Command Manually First

```bash
# Make sure you're in the project directory
php artisan reminders:send

# You should see output like:
# Found 0 pending reminder(s).
# No pending reminders to send.
```

### Step 4: Test Laravel Scheduler

```bash
# This will run all scheduled tasks that are due
php artisan schedule:run

# You should see:
# Running scheduled command: reminders:send
```

### Step 5: Set Up Cron Job

Laravel's scheduler needs to be called every minute by the server's cron.

**Edit crontab:**
```bash
crontab -e
```

**Add this line (replace `/var/www/laravel/bf_ss` with your actual path):**
```bash
* * * * * cd /var/www/laravel/bf_ss && php artisan schedule:run >> /dev/null 2>&1
```

**Or with full path to PHP (more reliable):**
```bash
* * * * * cd /var/www/laravel/bf_ss && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**To log scheduler output (for debugging):**
```bash
* * * * * cd /var/www/laravel/bf_ss && php artisan schedule:run >> /var/www/laravel/bf_ss/storage/logs/scheduler.log 2>&1
```

### Step 6: Verify Cron Job is Set

```bash
# List current cron jobs
crontab -l

# Check if cron service is running
sudo systemctl status cron
# or on Amazon Linux/CentOS:
sudo systemctl status crond
```

### Step 7: Test the Scheduler

**Wait 1-2 minutes, then check logs:**
```bash
# If you enabled logging
tail -f storage/logs/scheduler.log

# Or check Laravel logs
tail -f storage/logs/laravel.log
```

**Or test manually again:**
```bash
php artisan reminders:send
```

---

## Option 2: AWS Elastic Beanstalk

If you're using AWS Elastic Beanstalk:

### Step 1: Create Cron Configuration File

Create `.ebextensions/cron.config`:

```yaml
files:
  "/etc/cron.d/laravel-scheduler":
    mode: "000644"
    owner: root
    group: root
    content: |
      * * * * * webapp cd /var/app/current && php artisan schedule:run >> /dev/null 2>&1

commands:
  remove_old_cron:
    command: "rm -f /etc/cron.d/laravel-scheduler.bak"
```

### Step 2: Deploy to Elastic Beanstalk

Deploy your application with the cron configuration file included.

---

## Option 3: AWS ECS/Fargate (Docker)

If you're running Laravel in Docker containers:

### Create a Separate Scheduler Container

**Dockerfile.scheduler:**
```dockerfile
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install dependencies
RUN apk add --no-cache \
    curl \
    git \
    cronie

# Copy your application
COPY . .

# Create crontab file
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" > /etc/crontabs/root

# Start cron in foreground
CMD ["crond", "-f"]
```

**docker-compose.yml addition:**
```yaml
scheduler:
  build:
    context: .
    dockerfile: Dockerfile.scheduler
  volumes:
    - .:/var/www/html
  depends_on:
    - app
```

---

## Option 4: AWS Lambda + EventBridge (Serverless)

For a serverless approach:

### Step 1: Create Lambda Function

**Create `lambda/sendReminders.php`:**
```php
<?php

require '/var/task/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once '/var/task/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$artisan = $app->make(Illuminate\Contracts\Console\Kernel::class);

return function ($event) {
    $artisan->call('reminders:send');
    
    return [
        'statusCode' => 200,
        'body' => json_encode(['message' => 'Reminders sent'])
    ];
};
```

### Step 2: Set Up EventBridge Rule

1. Go to AWS EventBridge Console
2. Create Rule: `telegram-reminders`
3. Schedule: `rate(1 minute)`
4. Target: Your Lambda function

---

## Option 5: AWS Systems Manager (SSM) - For EC2

### Step 1: Create SSM Document

**Document name:** `LaravelReminderScheduler`

**Content:**
```json
{
  "schemaVersion": "2.2",
  "description": "Run Laravel reminder scheduler",
  "parameters": {},
  "mainSteps": [
    {
      "action": "aws:runShellScript",
      "name": "runReminders",
      "inputs": {
        "runCommand": [
          "cd /var/www/laravel/bf_ss && php artisan reminders:send"
        ]
      }
    }
  ]
}
```

### Step 2: Create Maintenance Window

1. Go to AWS Systems Manager → Maintenance Windows
2. Create Window: Run every 1 minute
3. Register target: Your EC2 instance
4. Register task: Use the SSM document

---

## Verification Steps

### 1. Check if Scheduler is Running

```bash
# On your server
ps aux | grep "schedule:run"

# Or check cron logs
sudo tail -f /var/log/cron
# or
sudo tail -f /var/log/syslog | grep CRON
```

### 2. Create a Test Reminder

1. Go to `/scorecard` page
2. Toggle an event's "Interrupted" switch ON
3. Set "Remind Me After" to 1 minute
4. Fill in old limits (required)
5. Submit the form

### 3. Wait and Check

**Wait 1-2 minutes, then:**

```bash
# Check database
php artisan tinker
```

```php
DB::table('event_reminders')->where('sent', false)->get();
DB::table('event_reminders')->where('sent', true)->orderBy('sent_at', 'desc')->first();
```

### 4. Check Telegram

Check your Telegram chat for the reminder message.

---

## Troubleshooting

### Cron Job Not Running

```bash
# Check cron service status
sudo systemctl status cron

# Restart cron service
sudo systemctl restart cron

# Check cron logs
sudo tail -f /var/log/cron
```

### Permission Issues

```bash
# Make sure Laravel has write permissions
sudo chown -R www-data:www-data /var/www/laravel/bf_ss/storage
sudo chmod -R 775 /var/www/laravel/bf_ss/storage

# Check PHP executable path
which php
# Use full path in crontab: /usr/bin/php
```

### Scheduler Not Finding Commands

```bash
# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Rebuild autoload
composer dump-autoload
```

### Check Scheduler Output

```bash
# Add logging to crontab
* * * * * cd /var/www/laravel/bf_ss && php artisan schedule:run >> /var/www/laravel/bf_ss/storage/logs/scheduler.log 2>&1

# Then watch the log
tail -f storage/logs/scheduler.log
```

### Test Reminder Command Directly

```bash
# Run the command manually
php artisan reminders:send

# With verbose output
php artisan reminders:send -v
```

### Check Environment Variables

```bash
# Make sure .env is readable
cat .env | grep TELEGRAM

# Verify config is cached correctly
php artisan config:show services.telegram
```

---

## Recommended Production Setup

For production, use this crontab entry with logging:

```bash
* * * * * cd /var/www/laravel/bf_ss && /usr/bin/php artisan schedule:run >> /var/www/laravel/bf_ss/storage/logs/scheduler.log 2>&1
```

**Benefits:**
- ✅ Logs all scheduler output
- ✅ Uses full PHP path (more reliable)
- ✅ Redirects errors to log file
- ✅ Easy to debug issues

---

## Monitoring

### Set Up CloudWatch Alarms (AWS)

1. Go to CloudWatch → Logs
2. Create Log Group: `/var/www/laravel/bf_ss/storage/logs/scheduler.log`
3. Create Metric Filter for errors
4. Set up alarm if errors occur

### Check Scheduler Status

```bash
# Check if scheduler ran in last 5 minutes
grep "$(date +\%Y-\%m-\%d\ \%H:\%M)" storage/logs/scheduler.log
```

---

## Security Best Practices

1. **Use IAM Roles** (not access keys) for AWS services
2. **Restrict cron permissions** to only necessary commands
3. **Monitor logs** for suspicious activity
4. **Use AWS Secrets Manager** for sensitive data (Telegram tokens)
5. **Enable CloudTrail** for audit logging

---

## Quick Reference Commands

```bash
# Test reminder command
php artisan reminders:send

# Test scheduler
php artisan schedule:run

# List scheduled tasks
php artisan schedule:list

# Edit crontab
crontab -e

# View crontab
crontab -l

# Check Laravel logs
tail -f storage/logs/laravel.log

# Check scheduler logs
tail -f storage/logs/scheduler.log
```

---

## Support

If you encounter issues:

1. Check `storage/logs/laravel.log`
2. Check `storage/logs/scheduler.log` (if enabled)
3. Verify `.env` has `TELEGRAM_BOT_TOKEN` and `TELEGRAM_CHAT_ID`
4. Test manually: `php artisan reminders:send`
5. Verify cron is running: `sudo systemctl status cron`

