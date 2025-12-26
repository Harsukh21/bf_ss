<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\TelegramService;

class CheckScorecardLabels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scorecard:check-labels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for events within 10 minutes of marketTime with missing scorecard labels and send Telegram notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for events within 10 minutes of marketTime with missing scorecard labels...');

        $now = Carbon::now();
        $tenMinutesFromNow = $now->copy()->addMinutes(10);

        // Get events where:
        // 1. marketTime is not null
        // 2. marketTime is between now and 10 minutes from now (current time <= 10 min of marketTime)
        // 3. At least one of the 4 required labels (4X, B2C, B2B, USDT) is NOT checked
        $requiredLabelKeys = ['4x', 'b2c', 'b2b', 'usdt'];

        $events = DB::table('events')
            ->whereNotNull('marketTime')
            ->whereBetween('marketTime', [
                $now->format('Y-m-d H:i:s'),
                $tenMinutesFromNow->format('Y-m-d H:i:s')
            ])
            ->select('id', 'exEventId', 'eventName', 'sportId', 'tournamentsName', 'marketTime', 'labels')
            ->get();

        if ($events->isEmpty()) {
            $this->info('No events found within 10 minutes of marketTime.');
            return 0;
        }

        // Filter events that have at least one missing required label
        $eventsToNotify = [];
        foreach ($events as $event) {
            // Parse labels from JSONB
            $labels = [];
            if ($event->labels) {
                $labels = is_string($event->labels) ? json_decode($event->labels, true) : $event->labels;
                if (!is_array($labels)) {
                    $labels = [];
                }
            }

            // Check if all 4 required labels are checked
            $allLabelsChecked = true;
            $missingLabels = [];
            foreach ($requiredLabelKeys as $labelKey) {
                $dbKey = strtolower($labelKey);
                $isChecked = isset($labels[$dbKey]) && (bool)$labels[$dbKey] === true;
                
                if (!$isChecked) {
                    $allLabelsChecked = false;
                    $missingLabels[] = strtoupper($labelKey);
                }
            }

            // If not all labels are checked, add to notification list
            if (!$allLabelsChecked) {
                $eventsToNotify[] = [
                    'event' => $event,
                    'missingLabels' => $missingLabels,
                ];
            }
        }

        if (empty($eventsToNotify)) {
            $this->info('All events have all required labels checked.');
            return 0;
        }

        // Get list of already notified event IDs from database
        // For scorecard notifications, we use exEventId as exMarketId (since there's no market)
        $eventIds = collect($eventsToNotify)->pluck('event.exEventId')->toArray();
        $notifiedEventIds = DB::table('telegram_notifications')
            ->where('notification_type', 'scorecard_labels')
            ->whereIn('exMarketId', $eventIds) // exMarketId stores exEventId for scorecard notifications
            ->pluck('exMarketId')
            ->toArray();

        // Filter out events that have already been notified
        $eventsToSend = array_filter($eventsToNotify, function($item) use ($notifiedEventIds) {
            return !in_array($item['event']->exEventId, $notifiedEventIds);
        });

        if (empty($eventsToSend)) {
            $this->info('All events have already been notified.');
            return 0;
        }

        $telegramService = new TelegramService();
        $notifiedCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($eventsToSend as $item) {
            $event = $item['event'];
            $missingLabels = $item['missingLabels'];

            // Double-check in database to prevent race conditions
            // For scorecard notifications, exMarketId stores exEventId
            $alreadyNotified = DB::table('telegram_notifications')
                ->where('exMarketId', $event->exEventId)
                ->where('notification_type', 'scorecard_labels')
                ->exists();

            if ($alreadyNotified) {
                $skippedCount++;
                continue;
            }

            // Format Telegram message
            $message = $this->formatScorecardLabelsMessage($event, $missingLabels);

            // Send Telegram notification to TELEGRAM_BOT_SC
            $chatId = config('services.telegram.sc_chat_id');
            $success = $telegramService->sendMessage($message, $chatId);

            if ($success) {
                // Mark as notified in database
                try {
                    DB::table('telegram_notifications')->insert([
                        'exMarketId' => $event->exEventId, // Use exEventId as exMarketId for scorecard notifications
                        'exEventId' => $event->exEventId,
                        'eventName' => $event->eventName,
                        'marketName' => 'N/A', // Not applicable for scorecard
                        'notification_type' => 'scorecard_labels',
                        'notified_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $notifiedCount++;
                    $this->info("✓ Notified: {$event->eventName} - Missing labels: " . implode(', ', $missingLabels));
                } catch (\Exception $e) {
                    $skippedCount++;
                    $this->warn("⚠ Skipped (duplicate): {$event->eventName}");
                }
            } else {
                $failedCount++;
                $this->error("✗ Failed: {$event->eventName}");
            }

            // Small delay to avoid rate limiting
            usleep(500000); // 0.5 seconds
        }

        $this->info("Completed: {$notifiedCount} notified, {$failedCount} failed, {$skippedCount} skipped (already notified)");
        return 0;
    }

    /**
     * Format the scorecard labels notification message
     *
     * @param object $event
     * @param array $missingLabels
     * @return string
     */
    protected function formatScorecardLabelsMessage($event, array $missingLabels): string
    {
        $eventName = $event->eventName ?? 'N/A';
        $sportId = $event->sportId ?? null;
        $sports = config('sports.sports', []);
        $sport = $sportId && isset($sports[$sportId]) ? $sports[$sportId] : 'N/A';
        $tournament = $event->tournamentsName ?? 'N/A';
        $marketTime = $event->marketTime 
            ? Carbon::parse($event->marketTime)->format('M d, Y • h:i A')
            : 'N/A';

        $missingLabelsText = implode(', ', $missingLabels);

        $lines = [
            "<b>⚠️⚠️⚠️ Missing Scorecard Labels ⚠️⚠️⚠️</b>",
            "",
            "<b>Sport:</b> " . $sport,
            "",
            "<b>Tournament:</b> " . $tournament,
            "",
            "<b>Event:</b> " . $eventName,
            "",
            "<b>Market Time:</b> " . $marketTime,
            "",
            "<b>Missing Labels:</b> " . $missingLabelsText,
            "",
            "<b>⚠️ Please check and update the scorecard labels before market time.</b>",
        ];

        return implode("\n", $lines);
    }
}

