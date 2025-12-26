<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Services\TelegramService;

class CheckCompletedMarkets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'markets:check-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for markets completed 10 minutes ago and send Telegram notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for markets completed 10 minutes ago...');

        // Calculate time range: markets completed exactly 10 minutes ago (with 1 minute window)
        $now = Carbon::now();
        $tenMinutesAgo = $now->copy()->subMinutes(10);
        $elevenMinutesAgo = $now->copy()->subMinutes(11);
        
        // Get markets that were completed 10 minutes ago (between 10-11 minutes ago)
        $completedMarkets = DB::table('market_lists')
            ->where('is_done', true)
            ->whereNotNull('completeTime')
            ->whereBetween('completeTime', [
                $elevenMinutesAgo->format('Y-m-d H:i:s'),
                $tenMinutesAgo->format('Y-m-d H:i:s')
            ])
            ->select('id', 'exMarketId', 'exEventId', 'eventName', 'marketName', 'sportName', 'tournamentsName', 'completeTime', 'status')
            ->get();
        
        if ($completedMarkets->isEmpty()) {
            $this->info('No markets completed 10 minutes ago found.');
            return 0;
        }

        // Get list of already notified market IDs from database
        $notifiedMarketIds = DB::table('telegram_notifications')
            ->where('notification_type', 'completed')
            ->whereIn('exMarketId', $completedMarkets->pluck('exMarketId')->toArray())
            ->pluck('exMarketId')
            ->toArray();
        
        // Filter out markets that have already been notified
        $marketsToNotify = $completedMarkets->filter(function($market) use ($notifiedMarketIds) {
            return !in_array($market->exMarketId, $notifiedMarketIds);
        });

        if ($marketsToNotify->isEmpty()) {
            $this->info('All markets have already been notified.');
            return 0;
        }

        $telegramService = new TelegramService();
        $notifiedCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($marketsToNotify as $market) {
            // Double-check in database to prevent race conditions
            $alreadyNotified = DB::table('telegram_notifications')
                ->where('exMarketId', $market->exMarketId)
                ->where('notification_type', 'completed')
                ->exists();
            
            if ($alreadyNotified) {
                $skippedCount++;
                continue; // Skip if already notified
            }

            // Format the completion date
            $completionDate = $market->completeTime 
                ? Carbon::parse($market->completeTime)->format('M d, Y • h:i A')
                : Carbon::now()->format('M d, Y • h:i A');

            // Format Telegram message
            $message = $this->formatCompletedMessage($market, $completionDate);

            // Send Telegram notification to TELEGRAM_CHAT_ID (not FROUD)
            $success = $telegramService->sendMessage($message, config('services.telegram.chat_id'));

            if ($success) {
                // Mark as notified in database (permanent record to prevent duplicates)
                try {
                    DB::table('telegram_notifications')->insert([
                        'exMarketId' => $market->exMarketId,
                        'exEventId' => $market->exEventId,
                        'eventName' => $market->eventName,
                        'marketName' => $market->marketName,
                        'notification_type' => 'completed',
                        'notified_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Also cache for faster future lookups
                    $cacheKey = "completed_notified_{$market->exMarketId}";
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
     * Format the completed market notification message
     *
     * @param object $market
     * @param string $date
     * @return string
     */
    protected function formatCompletedMessage($market, string $date): string
    {
        $eventName = $market->eventName ?? 'N/A';
        $marketName = $market->marketName ?? 'N/A';
        $sport = $market->sportName ?? 'N/A';
        $tournament = $market->tournamentsName ?? 'N/A';
        $exEventId = $market->exEventId ?? '';
        
        // Create clickable links for different platforms
        $trboLink = $exEventId ? "<a href=\"https://cbtfturbo.com/sports/details/{$exEventId}\">TURBO</a>" : 'TURBO';
        $fourXLink = $exEventId ? "<a href=\"https://d2.4xexch.com/sports/details/{$exEventId}\">4X</a>" : '4X';
        $usdtLink = $exEventId ? "<a href=\"https://usdtplayer.com/sports/details/{$exEventId}\">USDT</a>" : 'USDT';

        $lines = [
            "🔴🔴🔴 Market Completed/Closed 🔴🔴🔴",
            "",
            "<b>Sport:</b> " . $sport,
            "",
            "<b>Tournament:</b> " . $tournament,
            "",
            "<b>Event:</b> " . $eventName,
            "",
            " " . $trboLink . " || " . $fourXLink . " || " . $usdtLink . " ",
            "",
            "<b>Market:</b> " . $marketName,
            "",
            "<b>Completed Date:</b> " . $date,
            "",
            "<b>Status:</b> COMPLETED ✅",
        ];

        return implode("\n", $lines);
    }
}
