<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Services\TelegramService;

class CheckInPlayMarkets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'markets:check-inplay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for markets that turned in-play and send Telegram notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for markets that turned in-play...');

        // Get ALL markets with status = 3 (INPLAY) 
        // Only check markets where marketName is "Match Odds" or "Moneyline"
        // We'll filter out already-notified markets using database table for reliable duplicate prevention
        
        $allInPlayMarkets = DB::table('market_lists')
            ->where('status', 3) // INPLAY status
            ->whereIn('marketName', ['Match Odds', 'Moneyline']) // Only Match Odds and Moneyline markets
            ->select('id', 'exMarketId', 'exEventId', 'eventName', 'marketName', 'type', 'marketTime', 'sportName', 'updated_at', 'created_at')
            ->get();
        
        // Get list of already notified market IDs from database
        $notifiedMarketIds = DB::table('telegram_notifications')
            ->whereIn('exMarketId', $allInPlayMarkets->pluck('exMarketId')->toArray())
            ->pluck('exMarketId')
            ->toArray();
        
        // Filter out markets that have already been notified
        $inPlayMarkets = $allInPlayMarkets->filter(function($market) use ($notifiedMarketIds) {
            return !in_array($market->exMarketId, $notifiedMarketIds);
        });

        if ($inPlayMarkets->isEmpty()) {
            $this->info('No new in-play markets found.');
            return 0;
        }

        $telegramService = new TelegramService();
        $notifiedCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($inPlayMarkets as $market) {
            // Double-check in database to prevent race conditions
            $alreadyNotified = DB::table('telegram_notifications')
                ->where('exMarketId', $market->exMarketId)
                ->exists();
            
            if ($alreadyNotified) {
                $skippedCount++;
                continue; // Skip if already notified
            }

            // Format the date with bullet separator
            $marketDate = $market->marketTime 
                ? Carbon::parse($market->marketTime)->format('M d, Y • h:i A')
                : Carbon::parse($market->updated_at)->format('M d, Y • h:i A');

            // Format Telegram message
            $message = $this->formatInPlayMessage($market, $marketDate);

            // Send Telegram notification
            $success = $telegramService->sendMessage($message);

            if ($success) {
                // Mark as notified in database (permanent record to prevent duplicates)
                try {
                    DB::table('telegram_notifications')->insert([
                        'exMarketId' => $market->exMarketId,
                        'exEventId' => $market->exEventId,
                        'eventName' => $market->eventName,
                        'marketName' => $market->marketName,
                        'notified_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Also cache for faster future lookups
                    $cacheKey = "inplay_notified_{$market->exMarketId}";
                    Cache::put($cacheKey, true, now()->addHours(24));
                    
                    $notifiedCount++;
                    $this->info("✓ Notified: {$market->eventName} - {$market->marketName}");
                } catch (\Exception $e) {
                    // If insert fails (e.g., duplicate key), skip
                    $skippedCount++;
                    $this->warn("⚠ Skipped (duplicate): {$market->eventName} - {$market->marketName}");
                }
            } else {
                $failedCount++;
                $this->error("✗ Failed: {$market->eventName} - {$market->marketName}");
            }

            // Small delay to avoid rate limiting
            usleep(500000); // 0.5 seconds
        }

        $this->info("Completed: {$notifiedCount} notified, {$failedCount} failed, {$skippedCount} skipped (already notified)");
        return 0;
    }

    /**
     * Format the in-play notification message
     *
     * @param object $market
     * @param string $date
     * @return string
     */
    protected function formatInPlayMessage($market, string $date): string
    {
        // Use marketName directly (will be "Match Odds" or "Moneyline")
        $marketName = $market->marketName ?? 'N/A';
        $eventName = $market->eventName ?? 'N/A';
        $sport = $market->sportName ?? 'N/A';
        $exEventId = $market->exEventId ?? '';
        
        // Create event link - make the event name itself clickable
        $eventLink = $exEventId ? "https://cbtfturbo.com/sports/details/{$exEventId}" : '';
        $eventDisplay = $eventLink ? "<a href=\"{$eventLink}\">{$eventName}</a>" : $eventName;

        $lines = [
            "🟢🟢🟢 Event In-Play 🟢🟢🟢",
            "",
            "<b>Sport:</b> " . $sport,
            "",
            "<b>Event:</b> " . $eventDisplay,
            "",
            "<b>Market:</b> " . $marketName,
            "",
            "<b>Date:</b> " . $date,
            "",
            "<b>Status:</b> IN-PLAY 🔴🔥",
            "",
            "<b>🔍✅ Please check that SC has been added to all labels and that all rates are working correctly.</b>",
        ];

        return implode("\n", $lines);
    }
}
