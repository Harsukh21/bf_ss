<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramService;
use Carbon\Carbon;

class NotificationService
{
    protected $telegramService;

    public function __construct()
    {
        $this->telegramService = new TelegramService();
    }

    /**
     * Send notification to users
     */
    public function sendNotification($notificationId)
    {
        // Get notification
        $notification = DB::table('notifications')->where('id', $notificationId)->first();
        
        if (!$notification) {
            return;
        }

        // Get users assigned to this notification
        $users = DB::table('notification_user')
            ->join('users', 'notification_user.user_id', '=', 'users.id')
            ->where('notification_user.notification_id', $notificationId)
            ->select('users.*', 'notification_user.user_id')
            ->get();

        $deliveryMethods = json_decode($notification->delivery_methods ?? '[]', true);
        $isRecurring = in_array($notification->notification_type, ['daily', 'weekly', 'monthly']);

        foreach ($users as $user) {
            $deliveryStatus = [
                'push' => false,
                'telegram' => false,
                'login_popup' => false,
            ];

            // Send via Telegram if enabled and user has telegram_id
            if (in_array('telegram', $deliveryMethods) && $user->telegram_id) {
                try {
                    $message = "<b>{$notification->title}</b>\n\n{$notification->message}";
                    $telegramSent = $this->telegramService->sendMessage($message, $user->telegram_id);
                    $deliveryStatus['telegram'] = $telegramSent;
                } catch (\Exception $e) {
                    Log::error('Failed to send Telegram notification', [
                        'user_id' => $user->id,
                        'notification_id' => $notificationId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Push and login_popup are handled separately
            if (in_array('push', $deliveryMethods)) {
                $deliveryStatus['push'] = true; // Will be handled by frontend
            }

            if (in_array('login_popup', $deliveryMethods)) {
                $deliveryStatus['login_popup'] = true; // Will be shown on next login
            }

            // Update pivot table
            // For recurring notifications, reset is_read so it shows again
            $pivotData = [
                'is_delivered' => true,
                'delivered_at' => now(),
                'delivery_status' => json_encode($deliveryStatus),
                'updated_at' => now(),
            ];

            if ($isRecurring) {
                // Reset read status for recurring notifications
                $pivotData['is_read'] = false;
                $pivotData['read_at'] = null;
            }

            DB::table('notification_user')
                ->where('notification_id', $notificationId)
                ->where('user_id', $user->id)
                ->update($pivotData);
        }

        // Update notification status (don't mark as sent if it's recurring)
        if (!$isRecurring) {
            DB::table('notifications')
                ->where('id', $notificationId)
                ->update(['status' => 'sent', 'updated_at' => now()]);
        } else {
            // For recurring notifications, keep status as pending
            DB::table('notifications')
                ->where('id', $notificationId)
                ->update(['status' => 'pending', 'updated_at' => now()]);
        }
    }

    /**
     * Get pending notifications for a user
     */
    public function getPendingNotificationsForUser($userId)
    {
        return DB::table('notifications')
            ->join('notification_user', 'notifications.id', '=', 'notification_user.notification_id')
            ->where('notification_user.user_id', $userId)
            ->where('notification_user.is_read', false)
            ->whereIn('notifications.status', ['pending', 'sent'])
            ->where(function($query) {
                $query->whereNull('notifications.scheduled_at')
                      ->orWhere('notifications.scheduled_at', '<=', now());
            })
            ->whereRaw("(notifications.delivery_methods::jsonb ? 'login_popup')")
            ->select('notifications.*')
            ->orderBy('notifications.created_at', 'desc')
            ->get()
            ->toArray();
    }
}
