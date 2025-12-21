<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FA\Google2FA;
use App\Services\TelegramService;
use App\Services\TelegramUpdateService;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Get recent login history (last 10 logins)
        $loginHistory = $user->login_history ?? [];
        $recentLogins = array_slice(array_reverse($loginHistory), 0, 10);
        
        // Get active sessions
        $activeSessions = $user->active_sessions ?? [];
        
        return view('profile.index', compact('user', 'recentLogins', 'activeSessions'));
    }
    
    /**
     * Update user profile information
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username,' . Auth::id(), 'regex:/^[a-zA-Z0-9_]+$/'],
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'date_of_birth' => 'nullable|date|before:today',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'web_pin' => 'nullable|string|regex:/^[0-9]+$/|min:4',
            'telegram_id' => 'nullable|string|max:100',
        ], [
            'username.unique' => 'This username is already taken.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'web_pin.regex' => 'Web Pin must contain only numbers.',
            'web_pin.min' => 'Web Pin must be at least 4 digits.',
            'telegram_id.max' => 'Telegram ID must not exceed 100 characters.',
        ]);
        
        $user = Auth::user();
        $oldTelegramId = $user->telegram_id;
        $newTelegramId = trim($request->telegram_id ?? '');
        $needsValidation = false;
        $chatId = null;
        
        // Check if Telegram ID needs validation:
        // 1. New Telegram ID provided and different from old one
        // 2. Telegram ID exists but chat_id is missing
        if (!empty($newTelegramId)) {
            if ($newTelegramId !== $oldTelegramId || empty($user->telegram_chat_id)) {
                $needsValidation = true;
            }
        }
        
        // Validate Telegram ID if needed
        if ($needsValidation) {
            // First, sync Telegram updates to get latest chat_id mappings (with error handling)
            $updateService = new TelegramUpdateService();
            try {
                $syncResult = $updateService->syncUpdates();
                
                // Timeout is normal when there are no new updates - not an error
                // We'll continue with existing data in the database
                if (!$syncResult['success'] && !str_contains($syncResult['message'] ?? '', 'timeout') && !str_contains($syncResult['message'] ?? '', 'No new')) {
                    Log::warning('Telegram sync failed, but continuing with existing data', [
                        'user_id' => $user->id,
                        'telegram_id' => $newTelegramId,
                        'sync_message' => $syncResult['message'] ?? 'Unknown',
                    ]);
                }
            } catch (\Exception $e) {
                // Log but don't fail - we can still try to find existing chat_id from database
                // Connection timeouts are expected when there are no new updates
                if (!str_contains($e->getMessage(), 'timeout')) {
                    Log::warning('Telegram sync exception, continuing with lookup', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            // Check if chat_id exists for this telegram_id in database
            $chatId = $updateService->getChatIdByTelegramId($newTelegramId);
            
            if (!$chatId) {
                // Chat ID not found - user hasn't messaged the bot yet
                return back()
                    ->withErrors(['telegram_id' => 'Telegram ID not found. Please ensure you have sent a message (e.g., /start) to the bot first.'])
                    ->withInput()
                    ->with('telegram_validation_error', 'To use Telegram notifications, you must first start a conversation with the bot. Please follow these steps:<br><br>1. Click on the bot link below to open Telegram chat<br>2. Send a message (e.g., /start) to the bot<br>3. Wait a few seconds for the system to sync<br>4. Come back here and try again');
            }
            
            // Chat ID found - verify by sending a test message (only if telegram_id changed)
            if ($newTelegramId !== $oldTelegramId) {
                $telegramService = new TelegramService();
                $validationMessage = "✅ <b>Telegram ID Verified!</b>\n\n" .
                                   "Hello " . ($user->name ?? $user->email) . ",\n\n" .
                                   "Your Telegram ID has been successfully verified for " . config('app.name') . " notifications.\n\n" .
                                   "You will now receive notifications on this Telegram account.";
                
                // Use chat_id instead of telegram_id for sending
                $result = $telegramService->sendMessageWithDetails($validationMessage, (string)$chatId);
                
                if (!$result['success']) {
                    // Validation failed - return with error
                    $errorMessage = $result['error'] ?? 'Failed to send verification message to Telegram.';
                    
                    return back()
                        ->withErrors(['telegram_id' => $errorMessage])
                        ->withInput();
                }
            }
            
            // Validation succeeded - log the update
            Log::info('Telegram ID validated successfully', [
                'user_id' => $user->id,
                'telegram_id' => $newTelegramId,
                'telegram_chat_id' => $chatId,
                'was_update' => $newTelegramId === $oldTelegramId,
            ]);
        }
        
        // Prepare update data
        $updateData = [
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'date_of_birth' => $request->date_of_birth,
            'timezone' => $request->timezone,
            'language' => $request->language,
        ];

        // Only update web_pin if provided (will be auto-hashed by Eloquent cast)
        if ($request->filled('web_pin')) {
            $updateData['web_pin'] = $request->web_pin;
        }
        
        // Handle Telegram ID and chat_id
        if (empty($newTelegramId)) {
            // Removing Telegram ID
            if (!empty($oldTelegramId)) {
                $updateData['telegram_id'] = null;
                $updateData['telegram_chat_id'] = null;
            }
        } else {
            // Setting/updating Telegram ID
            $updateData['telegram_id'] = $newTelegramId;
            
            // Set chat_id if we found it during validation
            if ($chatId) {
                $updateData['telegram_chat_id'] = $chatId;
            } elseif ($newTelegramId === $oldTelegramId && $user->telegram_chat_id) {
                // Keep existing chat_id if telegram_id hasn't changed
                $updateData['telegram_chat_id'] = $user->telegram_chat_id;
            }
        }
        
        $user->update($updateData);
        
        // Prepare success message
        $successMessage = 'Profile updated successfully!';
        if (!empty($newTelegramId) && $newTelegramId !== $oldTelegramId) {
            $successMessage .= ' Your Telegram ID has been verified and is ready to receive notifications.';
        } elseif (empty($newTelegramId) && !empty($oldTelegramId)) {
            $successMessage .= ' Telegram ID has been removed.';
        }
        
        return redirect()->route('profile.index')->with('success', $successMessage);
    }
    
    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        $user->update([
            'password' => Hash::make($request->new_password),
            'password_changed_at' => now(),
        ]);
        
        return redirect()->route('profile.index')->with('success', 'Password updated successfully!');
    }
    
    /**
     * Display 2FA settings page
     */
    public function twoFactorIndex()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $google2fa = new Google2FA();
        
        // Generate QR code URL if 2FA is not enabled
        $qrCodeUrl = null;
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $google2fa->generateSecretKey();
            $user->save();
        }
        
        if ($user->two_factor_secret && !$user->two_factor_confirmed_at) {
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $user->two_factor_secret
            );
        }
        
        return view('profile.two-factor', compact('user', 'qrCodeUrl'));
    }
    
    /**
     * Enable 2FA
     */
    public function enableTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        $user = Auth::user();
        $google2fa = new Google2FA();
        
        if (!$user->two_factor_secret) {
            return back()->withErrors(['code' => '2FA secret not found. Please refresh the page.']);
        }
        
        if (!$google2fa->verifyKey($user->two_factor_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }
        
        // Generate recovery codes
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = strtoupper(substr(md5(uniqid()), 0, 8));
        }
        
        $user->update([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
        ]);
        
        return redirect()->route('profile.two-factor')->with('success', '2FA enabled successfully!')
            ->with('recovery_codes', $recoveryCodes);
    }
    
    /**
     * Disable 2FA
     */
    public function disableTwoFactor(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }
        
        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
        
        return redirect()->route('profile.two-factor')->with('success', '2FA disabled successfully!');
    }
    
    /**
     * Display security settings page
     */
    public function securityIndex()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Get recent login history (last 20 logins)
        $loginHistory = $user->login_history ?? [];
        $recentLogins = array_slice(array_reverse($loginHistory), 0, 20);
        
        // Get active sessions
        $activeSessions = $user->active_sessions ?? [];
        
        return view('profile.security', compact('user', 'recentLogins', 'activeSessions'));
    }
    
    /**
     * Logout from all devices
     */
    public function logoutAllDevices(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }
        
        // Clear all sessions except current one
        $currentSessionId = session()->getId();
        
        // Update user's active sessions to only include current session
        $user->update([
            'active_sessions' => [
                [
                    'session_id' => $currentSessionId,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'last_activity' => now()->toISOString(),
                ]
            ],
            'current_session_id' => $currentSessionId,
        ]);
        
        // Clear all other sessions from database
        DB::table('sessions')->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();
        
        return redirect()->route('profile.security')->with('success', 'Logged out from all other devices successfully!');
    }
    
    /**
     * Terminate specific session
     */
    public function terminateSession(Request $request, $sessionId)
    {
        $user = Auth::user();
        $activeSessions = $user->active_sessions ?? [];
        
        // Remove session from active sessions
        $activeSessions = array_filter($activeSessions, function ($session) use ($sessionId) {
            return $session['session_id'] !== $sessionId;
        });
        
        $user->update([
            'active_sessions' => array_values($activeSessions),
        ]);
        
        // Delete session from database
        DB::table('sessions')->where('id', $sessionId)->delete();
        
        return back()->with('success', 'Session terminated successfully!');
    }
    
    /**
     * Update login tracking when user logs in
     */
    public static function trackLogin($user, $request)
    {
        $sessionId = session()->getId();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        
        // Get existing login history
        $loginHistory = $user->login_history ?? [];
        
        // Add new login record
        $loginRecord = [
            'session_id' => $sessionId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'login_time' => now()->toISOString(),
            'location' => self::getLocationFromIP($ipAddress),
        ];
        
        array_unshift($loginHistory, $loginRecord);
        
        // Keep only last 50 login records
        $loginHistory = array_slice($loginHistory, 0, 50);
        
        // Get existing active sessions
        $activeSessions = $user->active_sessions ?? [];
        
        // Add current session to active sessions
        $currentSession = [
            'session_id' => $sessionId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'last_activity' => now()->toISOString(),
        ];
        
        // Remove any existing session with same ID
        $activeSessions = array_filter($activeSessions, function ($session) use ($sessionId) {
            return $session['session_id'] !== $sessionId;
        });
        
        // Add current session
        $activeSessions[] = $currentSession;
        
        // Keep only last 10 active sessions
        $activeSessions = array_slice($activeSessions, -10);
        
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
            'last_login_user_agent' => $userAgent,
            'login_history' => $loginHistory,
            'active_sessions' => array_values($activeSessions),
            'current_session_id' => $sessionId,
        ]);
    }
    
    /**
     * Get location from IP address (simplified)
     */
    private static function getLocationFromIP($ipAddress)
    {
        // This is a simplified implementation
        // In production, you might want to use a service like ipapi.co or similar
        if ($ipAddress === '127.0.0.1' || $ipAddress === '::1') {
            return 'Local Development';
        }
        
        // For now, just return the IP
        return $ipAddress;
    }
}