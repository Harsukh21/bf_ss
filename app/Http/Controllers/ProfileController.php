<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FA\Google2FA;

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
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'date_of_birth' => 'nullable|date|before:today',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'web_pin' => 'nullable|string|regex:/^[0-9]+$/|min:6',
            'telegram_id' => 'nullable|string|max:100',
        ], [
            'web_pin.regex' => 'Web Pin must contain only numbers.',
            'web_pin.min' => 'Web Pin must be at least 6 digits.',
            'telegram_id.max' => 'Telegram ID must not exceed 100 characters.',
        ]);
        
        $user = Auth::user();
        
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'date_of_birth' => $request->date_of_birth,
            'timezone' => $request->timezone,
            'language' => $request->language,
            'web_pin' => $request->web_pin,
            'telegram_id' => $request->telegram_id,
        ]);
        
        return redirect()->route('profile.index')->with('success', 'Profile updated successfully!');
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