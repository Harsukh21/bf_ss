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

        // Track if we've sent to default chat (to avoid duplicates)
        $sentToDefaultChat = false;

        foreach ($users as $user) {
            $deliveryStatus = [
                'push' => false,
                'telegram' => false,
                'login_popup' => false,
            ];

            // Send via Telegram if enabled
            if (in_array('telegram', $deliveryMethods)) {
                try {
                    $message = "<b>{$notification->title}</b>\n\n{$notification->message}";
                    
                    // Send to user's personal Telegram chat_id if available
                    if (!empty($user->telegram_chat_id)) {
                        // Send to user's personal Telegram using chat_id
                        $telegramSent = $this->telegramService->sendMessage($message, (string)$user->telegram_chat_id);
                        $deliveryStatus['telegram'] = $telegramSent;
                    } elseif (!empty($user->telegram_id)) {
                        // Fallback: Try to use telegram_id (for backward compatibility)
                        $telegramSent = $this->telegramService->sendMessage($message, $user->telegram_id);
                        $deliveryStatus['telegram'] = $telegramSent;
                    } elseif (!$sentToDefaultChat) {
                        // Send to default group/chat (only once per notification)
                        $telegramSent = $this->telegramService->sendMessage($message);
                        $deliveryStatus['telegram'] = $telegramSent;
                        $sentToDefaultChat = true;
                    } else {
                        // User doesn't have telegram_chat_id and we already sent to default chat
                        $deliveryStatus['telegram'] = true; // Mark as sent (via default chat)
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send Telegram notification', [
                        'user_id' => $user->id,
                        'notification_id' => $notificationId,
                        'telegram_chat_id' => $user->telegram_chat_id ?? null,
                        'error' => $e->getMessage(),
                    ]);
                    $deliveryStatus['telegram'] = false;
                }
            }

            // Push notifications - keep as false until frontend marks as delivered
            // Frontend will call markPushDelivered endpoint to update status
            if (in_array('push', $deliveryMethods)) {
                $deliveryStatus['push'] = false; // Will be marked as true by frontend via markPushDelivered endpoint
            }

            // Login popup - mark as true since it will be shown on next login
            if (in_array('login_popup', $deliveryMethods)) {
                $deliveryStatus['login_popup'] = true; // Will be shown on next login
            }

            // Determine if notification is delivered (at least one method succeeded)
            // If only push is selected, keep is_delivered as false until push is actually delivered
            $isDelivered = false;
            $hasPushOnly = in_array('push', $deliveryMethods) && count($deliveryMethods) === 1;
            $hasTelegram = in_array('telegram', $deliveryMethods);
            $hasLoginPopup = in_array('login_popup', $deliveryMethods);
            
            // Mark as delivered if:
            // 1. Telegram was sent successfully, OR
            // 2. Login popup is enabled (will show on next login), OR  
            // 3. Push is enabled AND at least one other method succeeded
            if ($hasTelegram && $deliveryStatus['telegram']) {
                $isDelivered = true;
            } elseif ($hasLoginPopup) {
                $isDelivered = true; // Login popup will show on next login
            } elseif (!$hasPushOnly && in_array('push', $deliveryMethods)) {
                // Push + other methods: mark as delivered if other method succeeded
                $isDelivered = ($hasTelegram && $deliveryStatus['telegram']) || ($hasLoginPopup);
            } elseif ($hasPushOnly) {
                // Push only: keep as false until frontend marks it as delivered
                $isDelivered = false;
            }

            // Update pivot table
            // For recurring notifications, reset is_read so it shows again
            $pivotData = [
                'is_delivered' => $isDelivered,
                'delivered_at' => $isDelivered ? now() : null,
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
            ->whereRaw("(notifications.delivery_methods::jsonb @> ?::jsonb)", [json_encode(['login_popup'])])
            ->select('notifications.*')
            ->orderBy('notifications.created_at', 'desc')
            ->get()
            ->map(function($notification) {
                return (array) $notification;
            })
            ->toArray();
    }
}
