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
            'password' => 'required',
        ]);

        // Attempt to authenticate using Laravel's built-in Auth system
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Track login information
            ProfileController::trackLogin($user, $request);
            
            return redirect()->intended('/dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
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
        
        $timezone = config('app.timezone', 'UTC');
        $now = Carbon::now($timezone);

        $statusMap = $this->getEventStatusMap();
        $statusStyles = $this->getEventStatusStyles();
        $matchOddsExpr = $this->getMatchOddsStatusExpression('e');

        $statusCounts = DB::table('events as e')
            ->selectRaw($matchOddsExpr . ' as match_status, COUNT(*) as total')
            ->groupBy('match_status')
            ->pluck('total', 'match_status')
            ->filter(fn ($_, $status) => !is_null($status))
            ->mapWithKeys(fn ($count, $status) => [(int) $status => $count])
            ->toArray();

        $totalEvents = DB::table('events')->count();

        $eventStats = [
            'total' => $totalEvents,
            'unsettled' => $statusCounts[1] ?? 0,
            'upcoming' => $statusCounts[2] ?? 0,
            'in_play' => $statusCounts[3] ?? 0,
            'settled' => $statusCounts[4] ?? 0,
            'voided' => $statusCounts[5] ?? 0,
            'removed' => $statusCounts[6] ?? 0,
        ];

        $flagCounts = [
            'highlight' => DB::table('events')->where('highlight', 1)->count(),
            'popular' => DB::table('events')->where('popular', 1)->count(),
        ];

        $marketStats = [
            'total' => DB::table('market_lists')->count(),
            'live' => DB::table('market_lists')->where('isLive', true)->count(),
            'pre' => DB::table('market_lists')->where('isPreBet', true)->count(),
            'completed' => DB::table('market_lists')->where('isCompleted', true)->count(),
        ];
        $marketStats['active'] = $marketStats['total'] - $marketStats['completed'];

        $recentEvents = DB::table('events')
            ->select([
                'id',
                'eventName',
                'tournamentsName',
                'IsSettle',
                'IsVoid',
                'IsUnsettle',
                'isCompleted',
                'highlight',
                'popular',
                'marketTime',
                'createdAt',
                DB::raw($this->getMatchOddsStatusExpression('events') . ' as "matchOddsStatus"'),
            ])
            ->orderByRaw('COALESCE("marketTime", "createdAt") DESC')
            ->limit(5)
            ->get()
            ->map(function ($event) use ($now, $timezone, $statusStyles) {
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

                $event->status_label = $status;
                $event->status_class = $statusClass;
                $event->display_time = $eventTime
                    ? $eventTime->format('M d, Y h:i A')
                    : ($event->createdAt ? Carbon::parse($event->createdAt, $timezone)->format('M d, Y h:i A') : null);

                return $event;
            });

        $statusBreakdown = collect($statusStyles)
            ->map(function ($style, $statusId) use ($eventStats) {
                $keyMap = [
                    1 => 'unsettled',
                    2 => 'upcoming',
                    3 => 'in_play',
                    4 => 'settled',
                    5 => 'voided',
                    6 => 'removed',
                ];

                $statKey = $keyMap[$statusId] ?? null;

                return [
                    'label' => $style['label'],
                    'count' => $statKey ? ($eventStats[$statKey] ?? 0) : 0,
                    'color' => $style['dot'],
                ];
            })
            ->values()
            ->all();

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

