#!/bin/bash
# =============================================================================
# Attendance Module Cleanup Script - Run on LIVE server
# =============================================================================
# This script removes the attendance module from the live environment:
#   - Drops the attendances, leaves, and holidays database tables
#   - Removes attendance-related permissions from the permissions table
#   - Removes associated role_permission pivot entries
#   - Removes migration records from the migrations table
#
# USAGE:
#   chmod +x cleanup_attendance_module.sh
#   ./cleanup_attendance_module.sh
#
# REQUIREMENTS: Run from the Laravel project root directory.
# =============================================================================

set -e

# Load .env for DB credentials
if [ -f .env ]; then
    export $(grep -v '^#' .env | grep -E '^(DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME|DB_PASSWORD)=' | xargs)
else
    echo "ERROR: .env file not found. Run this script from the Laravel project root."
    exit 1
fi

DB_PORT="${DB_PORT:-3306}"

echo "============================================="
echo "  Attendance Module Cleanup"
echo "============================================="
echo "Database : $DB_DATABASE"
echo "Host     : $DB_HOST:$DB_PORT"
echo ""
echo "This will permanently:"
echo "  - Drop tables: attendances, leaves, holidays"
echo "  - Delete attendance-related permissions"
echo "  - Remove migration records for the 3 tables"
echo ""
read -p "Are you sure you want to continue? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo "Aborted."
    exit 0
fi

echo ""
echo "Running cleanup SQL..."

mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" <<'SQL'

-- -------------------------------------------------------
-- 1. Drop attendance-related tables
-- -------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `attendances`;
DROP TABLE IF EXISTS `leaves`;
DROP TABLE IF EXISTS `holidays`;

SET FOREIGN_KEY_CHECKS = 1;

-- -------------------------------------------------------
-- 2. Remove attendance permissions from role_permission pivot
--    (adjust table name if yours differs)
-- -------------------------------------------------------
DELETE rp FROM `role_permission` rp
INNER JOIN `permissions` p ON p.id = rp.permission_id
WHERE p.`group` = 'Attendance';

-- -------------------------------------------------------
-- 3. Remove attendance permissions
-- -------------------------------------------------------
DELETE FROM `permissions`
WHERE `group` = 'Attendance';

-- -------------------------------------------------------
-- 4. Remove migration records
-- -------------------------------------------------------
DELETE FROM `migrations`
WHERE `migration` IN (
    '2026_03_04_225710_create_attendances_table',
    '2026_03_04_225710_create_leaves_table',
    '2026_03_04_225710_create_holidays_table'
);

SQL

echo ""
echo "============================================="
echo "  SQL cleanup complete."
echo "============================================="

echo ""
echo "Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "============================================="
echo "  Done! Attendance module fully removed."
echo "============================================="
