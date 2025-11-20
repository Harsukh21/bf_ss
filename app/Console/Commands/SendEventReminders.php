<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\TelegramService;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Telegram reminders for interrupted events';

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Find reminders that need to be sent
        // Check for reminders where reminder_time has passed and not yet sent
        $pendingReminders = DB::table('event_reminders')
            ->where('sent', false)
            ->where('reminder_time', '<=', $now->format('Y-m-d H:i:s'))
            ->get();

        if ($pendingReminders->isEmpty()) {
            $this->info('No pending reminders to send.');
            return 0;
        }

        $this->info("Found {$pendingReminders->count()} pending reminder(s).");

        // Get sport names mapping from config
        $sports = config('sports.sports', []);

        $sentCount = 0;
        $failedCount = 0;

        foreach ($pendingReminders as $reminder) {
            try {
                // Fetch event details with market old limits
                $event = DB::table('events')
                    ->where('exEventId', $reminder->exEventId)
                    ->first();

                if (!$event) {
                    $this->warn("Event not found for exEventId: {$reminder->exEventId}");
                    $this->markReminderAsFailed($reminder->id, 'Event not found');
                    $failedCount++;
                    continue;
                }

                // Check if event is still interrupted (might have been changed)
                if (!($event->is_interrupted ?? false)) {
                    $this->info("Event {$reminder->exEventId} is no longer interrupted. Skipping reminder.");
                    $this->markReminderAsSent($reminder->id, 'Event no longer interrupted');
                    continue;
                }

                // Format event data for reminder message
                $sportId = $event->sportId ?? null;
                $eventData = (object) [
                    'eventId' => $event->eventId ?? null,
                    'exEventId' => $event->exEventId,
                    'eventName' => $event->eventName ?? 'N/A',
                    'sportName' => $sportId && isset($sports[$sportId]) ? $sports[$sportId] : 'Unknown Sport',
                    'tournamentsName' => $event->tournamentsName ?? 'N/A',
                    'marketTime' => $event->marketTime ?? null,
                    'remind_me_after' => $event->remind_me_after ?? 0,
                    'formatted_market_time' => $event->marketTime 
                        ? Carbon::parse($event->marketTime)->format('M d, Y h:i A') 
                        : null,
                ];

                // Get market old limits for this event
                $marketOldLimits = DB::table('market_lists')
                    ->select('marketName', 'old_limit')
                    ->where('exEventId', $reminder->exEventId)
                    ->where('status', 3) // INPLAY status
                    ->whereNotNull('old_limit')
                    ->orderBy('marketName')
                    ->get()
                    ->map(function ($market) {
                        return (object) [
                            'marketName' => $market->marketName,
                            'old_limit' => $market->old_limit ?? 0,
                        ];
                    })
                    ->toArray();

                $eventData->market_old_limits = $marketOldLimits;

                // Send reminder via Telegram
                $success = $this->telegramService->sendEventReminder($eventData);

                if ($success) {
                    $this->markReminderAsSent($reminder->id);
                    $this->info("✓ Reminder sent for event: {$eventData->eventName} ({$reminder->exEventId})");
                    $sentCount++;
                } else {
                    $this->markReminderAsFailed($reminder->id, 'Failed to send Telegram message');
                    $this->error("✗ Failed to send reminder for event: {$eventData->eventName} ({$reminder->exEventId})");
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $this->markReminderAsFailed($reminder->id, $e->getMessage());
                $this->error("✗ Exception for reminder {$reminder->id}: {$e->getMessage()}");
                $failedCount++;
            }
        }

        $this->info("\nSummary:");
        $this->info("  Sent: {$sentCount}");
        $this->info("  Failed: {$failedCount}");

        return 0;
    }

    /**
     * Mark reminder as sent
     */
    protected function markReminderAsSent($reminderId, $note = null)
    {
        DB::table('event_reminders')
            ->where('id', $reminderId)
            ->update([
                'sent' => true,
                'sent_at' => now(),
                'error_message' => $note,
                'updated_at' => now(),
            ]);
    }

    /**
     * Mark reminder as failed
     */
    protected function markReminderAsFailed($reminderId, $errorMessage)
    {
        DB::table('event_reminders')
            ->where('id', $reminderId)
            ->update([
                'sent' => false,
                'error_message' => $errorMessage,
                'updated_at' => now(),
            ]);
    }
}
