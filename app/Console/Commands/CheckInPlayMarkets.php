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

        // Get markets with status = 3 (INPLAY) that were updated in the last 3 minutes
        // Only check markets with type = 'match_odds'
        // This catches markets that recently turned in-play
        $threeMinutesAgo = Carbon::now()->subMinutes(3);
        
        $inPlayMarkets = DB::table('market_lists')
            ->where('status', 3) // INPLAY status
            ->where('type', 'match_odds') // Only match_odds markets
            ->where('updated_at', '>=', $threeMinutesAgo)
            ->select('id', 'exMarketId', 'exEventId', 'eventName', 'marketName', 'marketTime', 'updated_at')
            ->get();

        if ($inPlayMarkets->isEmpty()) {
            $this->info('No new in-play markets found.');
            return 0;
        }

        $telegramService = new TelegramService();
        $notifiedCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($inPlayMarkets as $market) {
            // Check if we've already notified for this market (using cache key)
            $cacheKey = "inplay_notified_{$market->exMarketId}";
            
            if (Cache::has($cacheKey)) {
                $skippedCount++;
                continue; // Skip if already notified
            }

            // Format the date
            $marketDate = $market->marketTime 
                ? Carbon::parse($market->marketTime)->format('M d, Y h:i A')
                : Carbon::parse($market->updated_at)->format('M d, Y h:i A');

            // Format Telegram message
            $message = $this->formatInPlayMessage($market, $marketDate);

            // Send Telegram notification
            $success = $telegramService->sendMessage($message);

            if ($success) {
                // Mark as notified in cache (store for 24 hours to avoid duplicates)
                Cache::put($cacheKey, true, now()->addHours(24));
                $notifiedCount++;
                $this->info("✓ Notified: {$market->eventName} - {$market->marketName}");
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
        $lines = [
            "🎮 <b>Market Now In-Play</b> 🎮",
            "",
            "<b>Event:</b> " . ($market->eventName ?? 'N/A'),
            "<b>Market:</b> " . ($market->marketName ?? 'N/A'),
            "<b>Date:</b> " . $date,
            "",
            "Status changed to INPLAY",
        ];

        return implode("\n", $lines);
    }
}
