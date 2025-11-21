<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notificationService = new NotificationService();
        
        // Get notifications that are scheduled and due
        $notifications = DB::table('notifications')
            ->where('status', 'pending')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($notifications as $notification) {
            // Send notification
            $notificationService->sendNotification($notification->id);

            // Handle recurring notifications
            if ($notification->notification_type === 'daily') {
                // Schedule for next day
                $dailyTime = Carbon::parse($notification->daily_time);
                $nextScheduledAt = Carbon::now()->setTimeFromTimeString($dailyTime->format('H:i:s'))->addDay();
                
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update([
                        'status' => 'pending',
                        'scheduled_at' => $nextScheduledAt,
                        'updated_at' => now(),
                    ]);
            } elseif ($notification->notification_type === 'weekly') {
                // Schedule for next week
                $dayOfWeek = (int)$notification->weekly_day;
                $time = Carbon::parse($notification->weekly_time);
                $nextScheduledAt = Carbon::now()->next($dayOfWeek)->setTimeFromTimeString($time->format('H:i:s'));
                if ($nextScheduledAt->isPast()) {
                    $nextScheduledAt->addWeek();
                }
                
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update([
                        'status' => 'pending',
                        'scheduled_at' => $nextScheduledAt,
                        'updated_at' => now(),
                    ]);
            } elseif ($notification->notification_type === 'monthly') {
                // Schedule for next month
                $dayOfMonth = (int)$notification->monthly_day;
                $time = Carbon::parse($notification->monthly_time);
                $nextScheduledAt = Carbon::now()->startOfMonth()->addMonths(1)->addDays($dayOfMonth - 1);
                $nextScheduledAt->setTimeFromTimeString($time->format('H:i:s'));
                // Handle edge case where day doesn't exist in next month (e.g., Feb 31)
                if ($nextScheduledAt->day < $dayOfMonth) {
                    $nextScheduledAt->endOfMonth();
                }
                
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update([
                        'status' => 'pending',
                        'scheduled_at' => $nextScheduledAt,
                        'updated_at' => now(),
                    ]);
            }
        }

        $this->info('Processed ' . $notifications->count() . ' scheduled notifications.');
    }
}
