<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\ProfileController;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'login_method' => 'required|in:password,web_pin',
        ]);

        $loginMethod = $request->input('login_method', 'password');
        $email = $request->input('email');

        // Validate based on login method
        if ($loginMethod === 'password') {
            $request->validate([
                'password' => 'required',
            ]);
            
            // Attempt to authenticate using Laravel's built-in Auth system
            $credentials = $request->only('email', 'password');
            $remember = $request->has('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                $user = Auth::user();
                
                // Load permissions into cache on login
                $user->loadPermissionsIntoCache();
                $user->loadRolesIntoCache();
                
                // Track login information
                ProfileController::trackLogin($user, $request);
                
                return redirect()->intended('/dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email', 'login_method'));
            
        } else if ($loginMethod === 'web_pin') {
            $request->validate([
                'web_pin' => 'required|string|regex:/^[0-9]+$/|min:6',
            ], [
                'web_pin.regex' => 'Web PIN must contain only numbers.',
                'web_pin.min' => 'Web PIN must be at least 6 digits.',
            ]);
            
            // Find user by email
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])->withInput($request->only('email', 'login_method'));
            }
            
            // Get raw web_pin value from database (bypassing Eloquent casts)
            $userData = DB::table('users')->where('id', $user->id)->first();
            
            if (empty($userData->web_pin)) {
                return back()->withErrors([
                    'web_pin' => 'Web PIN is not set for this account. Please use password login or contact administrator.',
                ])->withInput($request->only('email', 'login_method'));
            }
            
            // Verify web_pin - handle both hashed and plain text (backward compatibility)
            $storedPin = $userData->web_pin;
            $inputPin = $request->input('web_pin');
            $isVerified = false;
            $needsHashing = false;
            
            // Check if stored PIN is already hashed (bcrypt hashes start with $2y$, $2a$, or $2b$ and are 60 chars)
            if (strlen($storedPin) >= 60 && (str_starts_with($storedPin, '$2y$') || str_starts_with($storedPin, '$2a$') || str_starts_with($storedPin, '$2b$'))) {
                // Already hashed - use Hash::check()
                $isVerified = Hash::check($inputPin, $storedPin);
            } else {
                // Plain text - compare directly (backward compatibility)
                $isVerified = ($storedPin === $inputPin);
                $needsHashing = true; // Mark for auto-hashing after verification
            }
            
            if (!$isVerified) {
                return back()->withErrors([
                    'web_pin' => 'The provided Web PIN is incorrect.',
                ])->withInput($request->only('email', 'login_method'));
            }
            
            // Auto-hash plain text web_pin for security (one-time migration)
            if ($needsHashing) {
                $user->web_pin = $inputPin; // Will be auto-hashed by Eloquent cast
                $user->save();
            }
            
            // Login the user
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();
            
            // Load permissions into cache on login
            $user->loadPermissionsIntoCache();
            $user->loadRolesIntoCache();
            
            // Track login information
            ProfileController::trackLogin($user, $request);
            
            return redirect()->intended('/dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Invalid login method.',
        ])->withInput($request->only('email', 'login_method'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear any cached data and prevent back button access
        $request->session()->flush();
        
        return redirect('/login')->with('success', 'You have been successfully logged out. Thank you for using our application!')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Sat, 01 Jan 1990 00:00:00 GMT',
                'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT'
            ]);
    }

    /**
     * Show dashboard (after login)
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Sat, 01 Jan 1990 00:00:00 GMT'
            ]);
        }
        
        // Eager load user roles for display
        $user = Auth::user();
        $user->load('roles');
        
        $timezone = config('app.timezone', 'UTC');
        $now = Carbon::now($timezone);
        $statusStyles = $this->getEventStatusStyles();
        $matchOddsExpr = $this->getMatchOddsStatusExpression('e');

        // Get event status counts using raw query
        $statusCountsQuery = "SELECT 
            ({$matchOddsExpr}) as match_status,
            COUNT(*) as total
        FROM events e
        WHERE ({$matchOddsExpr}) IS NOT NULL
        GROUP BY match_status";
        
        $statusCountsRaw = DB::select($statusCountsQuery);
        $statusCounts = [];
        foreach ($statusCountsRaw as $row) {
            $statusCounts[(int)$row->match_status] = (int)$row->total;
        }

        // Get total events count
        $totalEvents = (int)DB::selectOne("SELECT COUNT(*) as total FROM events")->total;

        $eventStats = [
            'total' => $totalEvents,
            'unsettled' => $statusCounts[1] ?? 0,
            'upcoming' => $statusCounts[2] ?? 0,
            'in_play' => $statusCounts[3] ?? 0,
            'settled' => $statusCounts[4] ?? 0,
            'voided' => $statusCounts[5] ?? 0,
            'removed' => $statusCounts[6] ?? 0,
        ];

        // Get flag counts in single query
        $flagCountsRaw = DB::selectOne("
            SELECT 
                COUNT(CASE WHEN highlight::int = 1 THEN 1 END) as highlight,
                COUNT(CASE WHEN popular::int = 1 THEN 1 END) as popular
            FROM events
        ");
        $flagCounts = [
            'highlight' => (int)$flagCountsRaw->highlight,
            'popular' => (int)$flagCountsRaw->popular,
        ];

        // Get market stats in single query
        $marketStatsRaw = DB::selectOne("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN \"isLive\" = true THEN 1 END) as live,
                COUNT(CASE WHEN \"isPreBet\" = true THEN 1 END) as pre,
                COUNT(CASE WHEN \"isCompleted\" = true THEN 1 END) as completed
            FROM market_lists
        ");
        $marketStats = [
            'total' => (int)$marketStatsRaw->total,
            'live' => (int)$marketStatsRaw->live,
            'pre' => (int)$marketStatsRaw->pre,
            'completed' => (int)$marketStatsRaw->completed,
            'active' => (int)$marketStatsRaw->total - (int)$marketStatsRaw->completed,
        ];

        // Get recent events with only needed fields
        $recentEventsQuery = "
            SELECT 
                e.id,
                e.\"eventName\",
                e.\"tournamentsName\",
                e.\"IsSettle\",
                e.\"IsVoid\",
                e.\"IsUnsettle\",
                e.\"isCompleted\",
                e.\"marketTime\",
                e.\"createdAt\",
                ({$matchOddsExpr}) as \"matchOddsStatus\"
            FROM events e
            ORDER BY COALESCE(e.\"marketTime\", e.\"createdAt\") DESC
            LIMIT 5
        ";
        
        $recentEventsRaw = DB::select($recentEventsQuery);
        $recentEvents = collect($recentEventsRaw)->map(function ($event) use ($now, $timezone, $statusStyles) {
            $eventTime = $event->marketTime ? Carbon::parse($event->marketTime, $timezone) : null;
            $matchStatus = $event->matchOddsStatus !== null ? (int) $event->matchOddsStatus : null;

            if ($matchStatus && isset($statusStyles[$matchStatus])) {
                $status = $statusStyles[$matchStatus]['label'];
                $statusClass = $statusStyles[$matchStatus]['badge'];
            } else {
                $status = 'Unknown';
                $statusClass = 'bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-200';

                if ($event->IsVoid) {
                    $status = 'Voided';
                    $statusClass = 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300';
                } elseif ($event->IsSettle) {
                    $status = 'Settled';
                    $statusClass = 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300';
                } elseif ($event->IsUnsettle) {
                    $status = 'Unsettled';
                    $statusClass = 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300';
                } elseif ($eventTime && $eventTime->gt($now)) {
                    $status = 'Upcoming';
                    $statusClass = 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300';
                } elseif ($eventTime && $eventTime->lte($now)) {
                    $status = 'In-Play';
                    $statusClass = 'bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300';
                }
            }

            return (object)[
                'id' => $event->id,
                'eventName' => $event->eventName,
                'tournamentsName' => $event->tournamentsName,
                'status_label' => $status,
                'status_class' => $statusClass,
                'display_time' => $eventTime
                    ? $eventTime->format('M d, Y h:i A')
                    : ($event->createdAt ? Carbon::parse($event->createdAt, $timezone)->format('M d, Y h:i A') : null),
            ];
        });

        // Build status breakdown
        $statusBreakdown = [];
        $keyMap = [1 => 'unsettled', 2 => 'upcoming', 3 => 'in_play', 4 => 'settled', 5 => 'voided', 6 => 'removed'];
        foreach ($statusStyles as $statusId => $style) {
            $statKey = $keyMap[$statusId] ?? null;
            $statusBreakdown[] = [
                'label' => $style['label'],
                'count' => $statKey ? ($eventStats[$statKey] ?? 0) : 0,
                'color' => $style['dot'],
            ];
        }

        return response()
            ->view('dashboard', [
                'eventStats' => $eventStats,
                'flagCounts' => $flagCounts,
                'marketStats' => $marketStats,
                'recentEvents' => $recentEvents,
                'statusBreakdown' => $statusBreakdown,
                'statusStyles' => $statusStyles,
            ])
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Sat, 01 Jan 1990 00:00:00 GMT'
            ]);
    }

    private function getEventStatusMap(): array
    {
        return [
            1 => 'Unsettled',
            2 => 'Upcoming',
            3 => 'In Play',
            4 => 'Settled',
            5 => 'Voided',
            6 => 'Removed',
        ];
    }

    private function getEventStatusStyles(): array
    {
        return [
            1 => [
                'label' => 'Unsettled',
                'badge' => 'bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300',
                'dot' => 'bg-purple-500',
            ],
            2 => [
                'label' => 'Upcoming',
                'badge' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300',
                'dot' => 'bg-yellow-500',
            ],
            3 => [
                'label' => 'In Play',
                'badge' => 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300',
                'dot' => 'bg-red-500',
            ],
            4 => [
                'label' => 'Settled',
                'badge' => 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300',
                'dot' => 'bg-green-500',
            ],
            5 => [
                'label' => 'Voided',
                'badge' => 'bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
                'dot' => 'bg-gray-500',
            ],
            6 => [
                'label' => 'Removed',
                'badge' => 'bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300',
                'dot' => 'bg-orange-500',
            ],
        ];
    }

    private function getMatchOddsStatusExpression(string $eventAlias = 'events'): string
    {
        $quotedAlias = '"' . str_replace('"', '""', $eventAlias) . '"';

        return '(SELECT ml."status"
            FROM market_lists ml
            WHERE ml."type" = \'match_odds\'
              AND ml."exEventId" = ' . $quotedAlias . '."exEventId"
            ORDER BY ml."id" DESC
            LIMIT 1)';
    }
}

