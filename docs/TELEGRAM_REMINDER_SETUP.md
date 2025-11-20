# Telegram Reminder Alert Setup

This document explains how to set up and use the Telegram reminder alert system for interrupted events.

## Overview

The Telegram reminder system automatically sends notifications to a Telegram chat after a specified time (`remind_me_after` minutes) when an event is marked as "interrupted" on the Scorecard page.

## Features

- ✅ Automatic reminder scheduling based on `remind_me_after` time
- ✅ Telegram bot integration for sending alerts
- ✅ Reminder tracking to avoid duplicate messages
- ✅ Error handling and logging
- ✅ Scheduled command runs every minute to check for pending reminders

## Setup Instructions

### 1. Create a Telegram Bot

1. Open Telegram and search for `@BotFather`
2. Send `/newbot` command
3. Follow the instructions to create a new bot
4. Copy the **Bot Token** (e.g., `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`)

### 2. Get Chat ID

You have two options:

**Option A: Get Your Personal Chat ID**
1. Search for `@userinfobot` on Telegram
2. Start a conversation and it will show your Chat ID

**Option B: Get Group Chat ID**
1. Add your bot to the group
2. Send a message to the group
3. Visit: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
4. Find the `chat.id` in the response (it might be negative for groups)

### 3. Configure Environment Variables

Add the following to your `.env` file:

```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here
```

**Example:**
```env
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_CHAT_ID=123456789
```

### 4. Run Database Migration

```bash
php artisan migrate
```

This will create the `event_reminders` table to track sent reminders.

### 5. Set Up Scheduled Task

The reminder command is automatically scheduled to run every minute via Laravel's scheduler.

**For Production on AWS:**
See detailed AWS setup guide: [AWS_TELEGRAM_SCHEDULER_SETUP.md](./AWS_TELEGRAM_SCHEDULER_SETUP.md)

**Quick Setup for EC2/Linux Server:**
Add the following cron entry to your server (run `crontab -e`):

```bash
* * * * * cd /var/www/laravel/bf_ss && php artisan schedule:run >> /dev/null 2>&1
```

**Or with logging (recommended for production):**
```bash
* * * * * cd /var/www/laravel/bf_ss && /usr/bin/php artisan schedule:run >> /var/www/laravel/bf_ss/storage/logs/scheduler.log 2>&1
```

**For Local Development:**
You can test the command manually:

```bash
php artisan reminders:send
```

## How It Works

### 1. Setting a Reminder

1. Go to the `/scorecard` page
2. Toggle the "Interrupted" switch ON for an event
3. Fill in the "Remind Me After" field (e.g., 5, 10, 15, 20, 25, or 30 minutes)
4. Submit the form

The system will:
- Calculate `reminder_time = current_time + remind_me_after minutes`
- Store this in the `event_reminders` table
- The scheduled command will check and send the reminder when the time arrives

### 2. Sending Reminders

The scheduled command (`reminders:send`) runs every minute and:
1. Checks for reminders where `reminder_time <= current_time` and `sent = false`
2. Fetches event details including market old limits
3. Formats a message with event information
4. Sends the message via Telegram
5. Marks the reminder as sent

### 3. Reminder Message Format

The Telegram message includes:
- 🔔 Event Reminder header
- Event name and ID
- Sport and Tournament
- Event time
- Market Old Limits (if any)
- Reminder time information

**Example Message:**
```
🔔 Event Reminder

Event: New Zealand v West Indies
Event ID: 34966420
Sport: Cricket
Tournament: One Day Internationals
Event Time: Nov 19, 2025 06:30 AM

Market Old Limits:
  • Match Odds: 1
  • Tied Match: 1

Reminder set for 5 minutes after interruption.
```

## Configuration

### Telegram Settings

Located in `config/services.php`:

```php
'telegram' => [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'chat_id' => env('TELEGRAM_CHAT_ID'),
],
```

## Troubleshooting

### Reminders Not Sending

1. **Check Environment Variables:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

2. **Test Telegram Connection:**
   ```bash
   php artisan tinker
   ```
   Then:
   ```php
   $service = new App\Services\TelegramService();
   $service->sendMessage('Test message');
   ```

3. **Check Scheduled Command:**
   ```bash
   php artisan reminders:send
   ```

4. **Check Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Common Issues

- **"Telegram configuration missing"**: Ensure `TELEGRAM_BOT_TOKEN` and `TELEGRAM_CHAT_ID` are set in `.env`
- **"Telegram API error"**: Verify bot token is correct and bot is active
- **"Reminders not running"**: Ensure cron is set up for `schedule:run`

## Testing

### Manual Test

1. Set up an event with reminder:
   - Go to `/scorecard`
   - Toggle interrupt ON
   - Set "Remind Me After" to 1 minute
   - Submit

2. Wait 1 minute or run manually:
   ```bash
   php artisan reminders:send
   ```

3. Check Telegram for the message

### Check Pending Reminders

```sql
SELECT * FROM event_reminders WHERE sent = false ORDER BY reminder_time;
```

### Check Sent Reminders

```sql
SELECT * FROM event_reminders WHERE sent = true ORDER BY sent_at DESC;
```

## Database Schema

### event_reminders Table

- `id`: Primary key
- `exEventId`: Event external ID
- `reminder_time`: When to send the reminder
- `sent`: Whether reminder was sent (boolean)
- `sent_at`: Timestamp when sent (nullable)
- `error_message`: Error message if failed (nullable)
- `created_at`: Record creation timestamp
- `updated_at`: Record update timestamp

## Files Created/Modified

- `app/Services/TelegramService.php` - Telegram service class
- `app/Console/Commands/SendEventReminders.php` - Scheduled command
- `app/Http/Controllers/ScorecardController.php` - Updated to create reminders
- `database/migrations/2025_11_20_085405_create_event_reminders_table.php` - Migration
- `config/services.php` - Added Telegram configuration
- `bootstrap/app.php` - Added scheduled command

## Support

For issues or questions, check:
- Laravel logs: `storage/logs/laravel.log`
- Telegram bot API: https://core.telegram.org/bots/api

