# Web PIN Security Implementation

## Overview
The `web_pin` field was previously stored as plain text in the database, which posed a security risk. This document outlines the security improvements implemented to hash `web_pin` values similar to how passwords are hashed.

## Security Improvements

### 1. **Hashed Storage**
- Added `'web_pin' => 'hashed'` to the `User` model's `$casts` array
- When `web_pin` is set via Eloquent models (`User::create()`, `$user->update()`), it is automatically hashed using Laravel's bcrypt hashing
- Hashed values are one-way encrypted, making it impossible to retrieve the original PIN

### 2. **Secure Verification**
- Updated `AuthController` to use `Hash::check()` for hashed web_pins with backward compatibility
- Updated `NotificationController` to use `Hash::check()` for web_pin verification with backward compatibility
- Automatically detects if web_pin is hashed or plain text
- Plain text web_pins are automatically hashed after successful verification (one-time migration)
- All web_pin verification now uses secure hash comparison for hashed values

### 3. **Conditional Updates**
- Modified `UserController` and `ProfileController` to only update `web_pin` when provided
- Prevents accidental clearing of existing web_pins when updating other fields
- Follows the same pattern as password updates

### 4. **Migration Command**
- Created `HashWebPins` command to hash existing plain text web_pins
- Command: `php artisan web-pins:hash`
- Includes dry-run mode: `php artisan web-pins:hash --dry-run`

## Implementation Details

### Files Modified

1. **`app/Models/User.php`**
   - Added `'web_pin' => 'hashed'` to `$casts` array

2. **`app/Http/Controllers/AuthController.php`**
   - Changed web_pin verification from direct comparison to `Hash::check()`

3. **`app/Http/Controllers/NotificationController.php`**
   - Added `Hash` facade import
   - Changed web_pin verification to use `Hash::check()`

4. **`app/Http/Controllers/UserController.php`**
   - Updated `store()` method to conditionally set web_pin
   - Updated `update()` method to only update web_pin if provided

5. **`app/Http/Controllers/ProfileController.php`**
   - Updated profile update to only update web_pin if provided

### New Files

1. **`app/Console/Commands/HashWebPins.php`**
   - Console command to hash existing plain text web_pins
   - Validates PIN format before hashing
   - Skips already hashed values
   - Provides detailed output and summary

## Usage

### Automatic Migration (Recommended)

The system now automatically hashes plain text web_pins when users log in:
- When a user logs in with web_pin, the system checks if it's hashed or plain text
- If plain text, it verifies against the plain text value
- After successful verification, it automatically hashes and saves the web_pin
- This ensures a seamless transition without requiring immediate batch migration

### Manual Batch Migration (Optional)

If you prefer to hash all web_pins at once before deployment, run the migration command:

```bash
# Test run (dry-run mode) - shows what would be hashed
php artisan web-pins:hash --dry-run

# Actually hash existing web_pins
php artisan web-pins:hash
```

**Note**: The automatic migration happens during login, so manual batch migration is optional.

### Important Notes

1. **No User Impact**: Users can continue using their existing web_pin values after hashing. The system will verify against the hash automatically.

2. **One-Way Process**: Once web_pins are hashed, the original plain text values cannot be retrieved. Make sure to run the command in a safe environment first.

3. **Automatic Hashing**: All new web_pins created or updated after this implementation will be automatically hashed by Laravel's Eloquent model casting.

4. **Validation**: The command validates that web_pins are numeric and at least 6 digits before hashing.

## Security Benefits

1. **Protection Against Database Breaches**: Even if the database is compromised, attackers cannot retrieve original web_pin values
2. **Industry Standard**: Uses bcrypt hashing, the same algorithm used for passwords
3. **One-Way Encryption**: Hashes cannot be reversed to original values
4. **Automatic Application**: All new web_pins are automatically hashed without additional code

## Testing

After implementing these changes:

1. Test web_pin login with existing users
2. Test creating new users with web_pin
3. Test updating user web_pin
4. Test web_pin verification in notifications
5. Verify hashed values are stored in the database

## Rollback Plan

If you need to rollback (not recommended for security reasons):

1. Remove `'web_pin' => 'hashed'` from `User` model casts
2. Revert verification logic to direct comparison (not recommended)
3. Restore from database backup if needed

**Warning**: Rolling back removes security protection. Only do this if absolutely necessary and with proper authorization.

