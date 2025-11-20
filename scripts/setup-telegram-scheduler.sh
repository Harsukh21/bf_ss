#!/bin/bash

# Telegram Scheduler Setup Script for AWS EC2
# This script helps set up the Laravel scheduler for Telegram reminders

set -e

PROJECT_DIR="/var/www/laravel/bf_ss"
PHP_PATH=$(which php)

echo "=========================================="
echo "Telegram Scheduler Setup for AWS"
echo "=========================================="
echo ""

# Check if we're in the project directory
if [ ! -f "$PROJECT_DIR/artisan" ]; then
    echo "❌ Error: Laravel project not found at $PROJECT_DIR"
    echo "Please update PROJECT_DIR in this script or run from project root"
    exit 1
fi

echo "✓ Project directory found: $PROJECT_DIR"
echo "✓ PHP path: $PHP_PATH"
echo ""

# Test the reminder command
echo "Testing reminder command..."
cd "$PROJECT_DIR"
if php artisan reminders:send > /dev/null 2>&1; then
    echo "✓ Reminder command works!"
else
    echo "⚠ Warning: Command returned non-zero exit code (this is OK if no reminders pending)"
fi
echo ""

# Test Laravel scheduler
echo "Testing Laravel scheduler..."
if php artisan schedule:run > /dev/null 2>&1; then
    echo "✓ Laravel scheduler works!"
else
    echo "⚠ Warning: Scheduler returned non-zero exit code"
fi
echo ""

# Check if cron is running
echo "Checking cron service..."
if systemctl is-active --quiet cron || systemctl is-active --quiet crond; then
    echo "✓ Cron service is running"
else
    echo "❌ Error: Cron service is not running"
    echo "Please start it with: sudo systemctl start cron (or crond)"
    exit 1
fi
echo ""

# Check current crontab
echo "Current crontab entries:"
crontab -l 2>/dev/null | grep -v "^#" | grep -v "^$" || echo "No cron entries found"
echo ""

# Ask user if they want to add the scheduler cron job
read -p "Do you want to add/update the Laravel scheduler cron job? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    # Check if entry already exists
    if crontab -l 2>/dev/null | grep -q "schedule:run"; then
        echo "⚠ Laravel scheduler entry already exists in crontab"
        read -p "Do you want to update it? (y/n) " -n 1 -r
        echo ""
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            echo "Skipping cron job update"
            exit 0
        fi
        # Remove old entry
        (crontab -l 2>/dev/null | grep -v "schedule:run") | crontab -
    fi
    
    # Create log directory if it doesn't exist
    mkdir -p "$PROJECT_DIR/storage/logs"
    chmod 775 "$PROJECT_DIR/storage/logs"
    
    # Add new cron entry with logging
    (crontab -l 2>/dev/null; echo "* * * * * cd $PROJECT_DIR && $PHP_PATH artisan schedule:run >> $PROJECT_DIR/storage/logs/scheduler.log 2>&1") | crontab -
    
    echo "✓ Laravel scheduler cron job added!"
    echo ""
    echo "Cron entry added:"
    echo "* * * * * cd $PROJECT_DIR && $PHP_PATH artisan schedule:run >> $PROJECT_DIR/storage/logs/scheduler.log 2>&1"
    echo ""
    echo "Scheduler logs will be written to: $PROJECT_DIR/storage/logs/scheduler.log"
    echo ""
    echo "To view scheduler logs:"
    echo "  tail -f $PROJECT_DIR/storage/logs/scheduler.log"
    echo ""
else
    echo "Skipping cron job setup"
fi

echo ""
echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Verify .env has TELEGRAM_BOT_TOKEN and TELEGRAM_CHAT_ID"
echo "2. Test the reminder command: php artisan reminders:send"
echo "3. Wait 1-2 minutes and check scheduler.log for activity"
echo "4. Create a test reminder on /scorecard page"
echo ""
echo "For more details, see: docs/AWS_TELEGRAM_SCHEDULER_SETUP.md"

