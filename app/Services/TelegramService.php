<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $chatId;
    protected $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Send a message to Telegram
     *
     * @param string $message
     * @param string|null $chatId
     * @return bool
     */
    public function sendMessage(string $message, ?string $chatId = null): bool
    {
        if (empty($this->botToken) || empty($chatId ?? $this->chatId)) {
            Log::warning('Telegram configuration missing. Bot token or chat ID not set.');
            return false;
        }

        try {
            $response = Http::post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $chatId ?? $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['ok']) && $result['ok'] === true) {
                    return true;
                }
            }

            Log::error('Telegram API error', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Telegram send message exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Send a formatted event reminder
     *
     * @param object $event
     * @return bool
     */
    public function sendEventReminder($event): bool
    {
        $message = $this->formatEventReminderMessage($event);
        return $this->sendMessage($message);
    }

    /**
     * Format event reminder message
     *
     * @param object $event
     * @return string
     */
    protected function formatEventReminderMessage($event): string
    {
        $lines = [
            "🔔 <b>Limit Reminder</b>",
            "",
            "<b>Event:</b> " . ($event->eventName ?? 'N/A'),
            "<b>Sport:</b> " . ($event->sportName ?? 'N/A'),
        ];

        if (!empty($event->market_old_limits) && is_array($event->market_old_limits)) {
            $lines[] = "";
            $lines[] = "<b>Market Old Limits:</b>";
            foreach ($event->market_old_limits as $market) {
                $lines[] = "  • " . ($market->marketName ?? 'N/A') . ": " . ($market->old_limit ?? 0);
            }
        }

        $lines[] = "";
        $lines[] = "<i>Reminder set for " . ($event->remind_me_after ?? 0) . " minutes.</i>";
        $lines[] = "<b>I have set a 0–1 limit in the market for the event mentioned above.</b>";

        return implode("\n", $lines);
    }
}

