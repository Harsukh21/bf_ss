#!/bin/bash
# =============================================================================
# Attendance Module Cleanup Script - Run on LIVE server (PostgreSQL)
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

DB_PORT="${DB_PORT:-5432}"

echo "============================================="
echo "  Attendance Module Cleanup (PostgreSQL)"
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

PGPASSWORD="$DB_PASSWORD" psql \
    -h "$DB_HOST" \
    -p "$DB_PORT" \
    -U "$DB_USERNAME" \
    -d "$DB_DATABASE" \
    <<'SQL'

-- -------------------------------------------------------
-- 1. Drop attendance-related tables
-- -------------------------------------------------------
DROP TABLE IF EXISTS "attendances" CASCADE;
DROP TABLE IF EXISTS "leaves" CASCADE;
DROP TABLE IF EXISTS "holidays" CASCADE;

-- -------------------------------------------------------
-- 2. Remove attendance permissions from role_permission pivot
-- -------------------------------------------------------
DELETE FROM "role_permission"
WHERE "permission_id" IN (
    SELECT id FROM "permissions" WHERE "group" = 'Attendance'
);

-- -------------------------------------------------------
-- 3. Remove attendance permissions
-- -------------------------------------------------------
DELETE FROM "permissions"
WHERE "group" = 'Attendance';

-- -------------------------------------------------------
-- 4. Remove migration records
-- -------------------------------------------------------
DELETE FROM "migrations"
WHERE "migration" IN (
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
