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

        $eventStats = [
            'total' => DB::table('events')->count(),
            'upcoming' => DB::table('events')
                ->whereNotNull('marketTime')
                ->where('marketTime', '>', $now)
                ->count(),
            'in_play' => DB::table('events')
                ->whereNotNull('marketTime')
                ->where('marketTime', '<=', $now)
                ->where('IsSettle', 0)
                ->where('IsVoid', 0)
                ->where('IsUnsettle', 0)
                ->count(),
            'settled' => DB::table('events')
                ->where('IsSettle', 1)
                ->where('IsVoid', 0)
                ->count(),
            'unsettled' => DB::table('events')
                ->where('IsUnsettle', 1)
                ->where('IsSettle', 0)
                ->where('IsVoid', 0)
                ->count(),
            'closed' => DB::table('events')
                ->where('isCompleted', true)
                ->count(),
            'voided' => DB::table('events')
                ->where('IsVoid', 1)
                ->count(),
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
            ])
            ->orderByRaw('COALESCE("marketTime", "createdAt") DESC')
            ->limit(5)
            ->get()
            ->map(function ($event) use ($now, $timezone) {
                $eventTime = $event->marketTime ? Carbon::parse($event->marketTime, $timezone) : null;
                $status = 'Scheduled';
                $statusClass = 'bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-200';

                if ($event->IsVoid) {
                    $status = 'Voided';
                    $statusClass = 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300';
                } elseif ($event->IsSettle) {
                    $status = 'Settled';
                    $statusClass = 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300';
                } elseif ($event->isCompleted) {
                    $status = 'Closed';
                    $statusClass = 'bg-indigo-100 dark:bg-indigo-900/20 text-indigo-800 dark:text-indigo-300';
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

                $event->status_label = $status;
                $event->status_class = $statusClass;
                $event->display_time = $eventTime
                    ? $eventTime->format('M d, Y h:i A')
                    : ($event->createdAt ? Carbon::parse($event->createdAt, $timezone)->format('M d, Y h:i A') : null);

                return $event;
            });

        $statusBreakdown = [
            [
                'label' => 'Upcoming',
                'count' => $eventStats['upcoming'],
                'color' => 'bg-blue-500',
            ],
            [
                'label' => 'In-Play',
                'count' => $eventStats['in_play'],
                'color' => 'bg-purple-500',
            ],
            [
                'label' => 'Settled',
                'count' => $eventStats['settled'],
                'color' => 'bg-green-500',
            ],
            [
                'label' => 'Unsettled',
                'count' => $eventStats['unsettled'],
                'color' => 'bg-yellow-500',
            ],
            [
                'label' => 'Closed',
                'count' => $eventStats['closed'],
                'color' => 'bg-indigo-500',
            ],
            [
                'label' => 'Voided',
                'count' => $eventStats['voided'],
                'color' => 'bg-red-500',
            ],
        ];

        return response()
            ->view('dashboard', [
                'eventStats' => $eventStats,
                'flagCounts' => $flagCounts,
                'marketStats' => $marketStats,
                'recentEvents' => $recentEvents,
                'statusBreakdown' => $statusBreakdown,
            ])
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Sat, 01 Jan 1990 00:00:00 GMT'
            ]);
    }
}

