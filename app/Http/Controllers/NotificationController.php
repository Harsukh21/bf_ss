<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('notifications')
            ->leftJoin('users as creator', 'notifications.created_by', '=', 'creator.id')
            ->select(
                'notifications.*',
                'creator.name as creator_name',
                'creator.email as creator_email'
            );

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('notifications.title', 'ILIKE', "%{$search}%")
                  ->orWhere('notifications.message', 'ILIKE', "%{$search}%");
            });
        }

        // Notification type filter
        if ($request->filled('notification_type')) {
            $query->where('notifications.notification_type', $request->notification_type);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('notifications.status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('notifications.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('notifications.created_at', '<=', $request->date_to);
        }

        // Scheduled date filter
        if ($request->filled('scheduled_from')) {
            $query->whereDate('notifications.scheduled_at', '>=', $request->scheduled_from);
        }
        if ($request->filled('scheduled_to')) {
            $query->whereDate('notifications.scheduled_at', '<=', $request->scheduled_to);
        }

        // Get total count for pagination
        $total = $query->count();
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        // Get paginated results
        $notifications = $query->orderBy('notifications.created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        // Calculate read statistics and attach user data for each notification
        foreach ($notifications as $notification) {
            // Get read count
            $readCount = DB::table('notification_user')
                ->where('notification_id', $notification->id)
                ->where('is_read', true)
                ->count();

            // Get total user count
            $totalUsers = DB::table('notification_user')
                ->where('notification_id', $notification->id)
                ->count();

            $notification->read_count = $readCount;
            $notification->unread_count = $totalUsers - $readCount;

            // Parse JSON fields
            $notification->delivery_methods = json_decode($notification->delivery_methods ?? '[]', true);
            
            // Convert timestamps
            $notification->created_at = $notification->created_at ? Carbon::parse($notification->created_at) : null;
            $notification->updated_at = $notification->updated_at ? Carbon::parse($notification->updated_at) : null;
            $notification->scheduled_at = $notification->scheduled_at ? Carbon::parse($notification->scheduled_at) : null;
            $notification->daily_time = $notification->daily_time ? Carbon::parse($notification->daily_time) : null;
            $notification->weekly_time = $notification->weekly_time ? Carbon::parse($notification->weekly_time) : null;
            $notification->monthly_time = $notification->monthly_time ? Carbon::parse($notification->monthly_time) : null;
        }

        // Create paginator manually
        $notifications = new \Illuminate\Pagination\LengthAwarePaginator(
            $notifications,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        $users = DB::table('users')
            ->whereNotNull('email_verified_at')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
        
        return view('notifications.create', compact('users'));
    }

    /**
     * Store a newly created notification in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'notification_type' => 'required|in:instant,after_minutes,daily,weekly,monthly,after_hours',
            'duration_value' => 'nullable|integer|min:1|required_if:notification_type,after_minutes,after_hours',
            'daily_time' => 'nullable|required_if:notification_type,daily|date_format:H:i',
            'weekly_day' => 'nullable|required_if:notification_type,weekly|integer|min:0|max:6',
            'weekly_time' => 'nullable|required_if:notification_type,weekly|date_format:H:i',
            'monthly_day' => 'nullable|required_if:notification_type,monthly|integer|min:1|max:31',
            'monthly_time' => 'nullable|required_if:notification_type,monthly|date_format:H:i',
            'delivery_methods' => 'required|array|min:1',
            'delivery_methods.*' => 'in:push,telegram,login_popup',
            'requires_web_pin' => 'boolean',
        ]);

        // Calculate scheduled_at based on notification_type
        $scheduledAt = null;
        if ($validated['notification_type'] === 'instant') {
            $scheduledAt = now();
        } elseif ($validated['notification_type'] === 'after_minutes') {
            $scheduledAt = now()->addMinutes($validated['duration_value']);
        } elseif ($validated['notification_type'] === 'after_hours') {
            $scheduledAt = now()->addHours($validated['duration_value']);
        } elseif ($validated['notification_type'] === 'daily') {
            $scheduledAt = Carbon::parse($validated['daily_time'])->setDate(now()->year, now()->month, now()->day);
            if ($scheduledAt->isPast()) {
                $scheduledAt->addDay();
            }
        } elseif ($validated['notification_type'] === 'weekly') {
            $dayOfWeek = (int)$validated['weekly_day'];
            $time = Carbon::parse($validated['weekly_time']);
            $scheduledAt = Carbon::now()->next($dayOfWeek);
            $scheduledAt->setTimeFromTimeString($time->format('H:i:s'));
            if ($scheduledAt->isPast() && $scheduledAt->dayOfWeek === Carbon::now()->dayOfWeek) {
                $scheduledAt->addWeek();
            }
        } elseif ($validated['notification_type'] === 'monthly') {
            $dayOfMonth = (int)$validated['monthly_day'];
            $time = Carbon::parse($validated['monthly_time']);
            $scheduledAt = Carbon::now()->startOfMonth()->addDays($dayOfMonth - 1);
            $scheduledAt->setTimeFromTimeString($time->format('H:i:s'));
            if ($scheduledAt->isPast()) {
                $scheduledAt->addMonth();
                if ($scheduledAt->day < $dayOfMonth) {
                    $scheduledAt->endOfMonth();
                }
            }
        }

        // Insert notification
        $notificationId = DB::table('notifications')->insertGetId([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'notification_type' => $validated['notification_type'],
            'duration_value' => $validated['duration_value'] ?? null,
            'daily_time' => $validated['daily_time'] ?? null,
            'weekly_day' => $validated['weekly_day'] ?? null,
            'weekly_time' => $validated['weekly_time'] ?? null,
            'monthly_day' => $validated['monthly_day'] ?? null,
            'monthly_time' => $validated['monthly_time'] ?? null,
            'scheduled_at' => $scheduledAt,
            'delivery_methods' => json_encode($validated['delivery_methods']),
            'status' => 'pending',
            'created_by' => Auth::id(),
            'requires_web_pin' => $request->has('requires_web_pin') ? true : false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attach users to notification
        $pivotData = [];
        foreach ($validated['user_ids'] as $userId) {
            $pivotData[] = [
                'notification_id' => $notificationId,
                'user_id' => $userId,
                'is_read' => false,
                'is_delivered' => false,
                'delivery_status' => json_encode([
                    'push' => false,
                    'telegram' => false,
                    'login_popup' => false,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('notification_user')->insert($pivotData);

        // If instant, send immediately
        if ($validated['notification_type'] === 'instant') {
            $notificationService = new NotificationService();
            $notificationService->sendNotification($notificationId);
        }

        return redirect()->route('notifications.index')
            ->with('success', 'Notification created successfully!');
    }

    /**
     * Mark notification as read after web_pin verification
     */
    public function markAsRead(Request $request, $id)
    {
        $request->validate([
            'web_pin' => 'required|string',
        ]);

        $user = Auth::user();

        // Verify web_pin
        $userData = DB::table('users')->where('id', $user->id)->first();
        if (!$userData->web_pin || $userData->web_pin !== $request->web_pin) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Web PIN',
            ], 422);
        }

        // Mark notification as read
        DB::table('notification_user')
            ->where('notification_id', $id)
            ->where('user_id', $user->id)
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Show the form for editing the specified notification.
     */
    public function edit($id)
    {
        $notification = DB::table('notifications')->where('id', $id)->first();
        
        if (!$notification) {
            abort(404);
        }

        // Get assigned user IDs
        $assignedUserIds = DB::table('notification_user')
            ->where('notification_id', $id)
            ->pluck('user_id')
            ->toArray();

        $users = DB::table('users')
            ->whereNotNull('email_verified_at')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Parse JSON and convert timestamps
        $notification->delivery_methods = json_decode($notification->delivery_methods ?? '[]', true);
        $notification->assigned_user_ids = $assignedUserIds;
        $notification->created_at = $notification->created_at ? Carbon::parse($notification->created_at) : null;
        $notification->updated_at = $notification->updated_at ? Carbon::parse($notification->updated_at) : null;
        $notification->scheduled_at = $notification->scheduled_at ? Carbon::parse($notification->scheduled_at) : null;
        $notification->daily_time = $notification->daily_time ? Carbon::parse($notification->daily_time) : null;
        $notification->weekly_time = $notification->weekly_time ? Carbon::parse($notification->weekly_time) : null;
        $notification->monthly_time = $notification->monthly_time ? Carbon::parse($notification->monthly_time) : null;

        return view('notifications.edit', compact('notification', 'users'));
    }

    /**
     * Update the specified notification in storage.
     */
    public function update(Request $request, $id)
    {
        $notification = DB::table('notifications')->where('id', $id)->first();
        
        if (!$notification) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'notification_type' => 'required|in:instant,after_minutes,daily,weekly,monthly,after_hours',
            'duration_value' => 'nullable|integer|min:1|required_if:notification_type,after_minutes,after_hours',
            'daily_time' => 'nullable|required_if:notification_type,daily|date_format:H:i',
            'weekly_day' => 'nullable|required_if:notification_type,weekly|integer|min:0|max:6',
            'weekly_time' => 'nullable|required_if:notification_type,weekly|date_format:H:i',
            'monthly_day' => 'nullable|required_if:notification_type,monthly|integer|min:1|max:31',
            'monthly_time' => 'nullable|required_if:notification_type,monthly|date_format:H:i',
            'delivery_methods' => 'required|array|min:1',
            'delivery_methods.*' => 'in:push,telegram,login_popup',
            'requires_web_pin' => 'boolean',
        ]);

        // Calculate scheduled_at based on notification_type
        $scheduledAt = null;
        if ($validated['notification_type'] === 'instant') {
            $scheduledAt = now();
        } elseif ($validated['notification_type'] === 'after_minutes') {
            $scheduledAt = now()->addMinutes($validated['duration_value']);
        } elseif ($validated['notification_type'] === 'after_hours') {
            $scheduledAt = now()->addHours($validated['duration_value']);
        } elseif ($validated['notification_type'] === 'daily') {
            $scheduledAt = Carbon::parse($validated['daily_time'])->setDate(now()->year, now()->month, now()->day);
            if ($scheduledAt->isPast()) {
                $scheduledAt->addDay();
            }
        } elseif ($validated['notification_type'] === 'weekly') {
            $dayOfWeek = (int)$validated['weekly_day'];
            $time = Carbon::parse($validated['weekly_time']);
            $scheduledAt = Carbon::now()->next($dayOfWeek);
            $scheduledAt->setTimeFromTimeString($time->format('H:i:s'));
            if ($scheduledAt->isPast() && $scheduledAt->dayOfWeek === Carbon::now()->dayOfWeek) {
                $scheduledAt->addWeek();
            }
        } elseif ($validated['notification_type'] === 'monthly') {
            $dayOfMonth = (int)$validated['monthly_day'];
            $time = Carbon::parse($validated['monthly_time']);
            $scheduledAt = Carbon::now()->startOfMonth()->addDays($dayOfMonth - 1);
            $scheduledAt->setTimeFromTimeString($time->format('H:i:s'));
            if ($scheduledAt->isPast()) {
                $scheduledAt->addMonth();
                if ($scheduledAt->day < $dayOfMonth) {
                    $scheduledAt->endOfMonth();
                }
            }
        }

        // Update notification
        DB::table('notifications')->where('id', $id)->update([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'notification_type' => $validated['notification_type'],
            'duration_value' => $validated['duration_value'] ?? null,
            'daily_time' => $validated['daily_time'] ?? null,
            'weekly_day' => $validated['weekly_day'] ?? null,
            'weekly_time' => $validated['weekly_time'] ?? null,
            'monthly_day' => $validated['monthly_day'] ?? null,
            'monthly_time' => $validated['monthly_time'] ?? null,
            'scheduled_at' => $scheduledAt,
            'delivery_methods' => json_encode($validated['delivery_methods']),
            'requires_web_pin' => $request->has('requires_web_pin') ? true : false,
            'updated_at' => now(),
        ]);

        // Sync users - delete old and insert new
        DB::table('notification_user')->where('notification_id', $id)->delete();
        
        $pivotData = [];
        foreach ($validated['user_ids'] as $userId) {
            $pivotData[] = [
                'notification_id' => $id,
                'user_id' => $userId,
                'is_read' => false,
                'is_delivered' => false,
                'delivery_status' => json_encode([
                    'push' => false,
                    'telegram' => false,
                    'login_popup' => false,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if (!empty($pivotData)) {
            DB::table('notification_user')->insert($pivotData);
        }

        return redirect()->route('notifications.index')
            ->with('success', 'Notification updated successfully!');
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy($id)
    {
        // Delete pivot records first
        DB::table('notification_user')->where('notification_id', $id)->delete();
        
        // Delete notification
        DB::table('notifications')->where('id', $id)->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted successfully!');
    }

    /**
     * Get pending notifications for authenticated user
     */
    public function getPendingNotifications(Request $request)
    {
        $user = Auth::user();
        $notificationService = new NotificationService();
        
        $notifications = $notificationService->getPendingNotificationsForUser($user->id);

        return response()->json([
            'success' => true,
            'notifications' => array_map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'requires_web_pin' => $notification->requires_web_pin,
                    'created_at' => Carbon::parse($notification->created_at)->format('Y-m-d H:i:s'),
                ];
            }, $notifications),
        ]);
    }
}
