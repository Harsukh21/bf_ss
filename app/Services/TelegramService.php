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
            $targetChatId = $chatId ?? $this->chatId;
            $response = Http::post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $targetChatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['ok']) && $result['ok'] === true) {
                    return true;
                }
            }

            // Log detailed error
            $errorResponse = $response->json();
            Log::error('Telegram API error', [
                'response' => $errorResponse,
                'status' => $response->status(),
                'chat_id' => $targetChatId,
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Telegram send message exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'chat_id' => $chatId ?? $this->chatId,
            ]);

            return false;
        }
    }

    /**
     * Send a message and return detailed error information
     *
     * @param string $message
     * @param string|null $chatId
     * @return array Returns ['success' => bool, 'error' => string|null]
     */
    public function sendMessageWithDetails(string $message, ?string $chatId = null): array
    {
        if (empty($this->botToken) || empty($chatId ?? $this->chatId)) {
            return [
                'success' => false,
                'error' => 'Telegram configuration missing. Bot token or chat ID not set.'
            ];
        }

        try {
            $targetChatId = $chatId ?? $this->chatId;
            $response = Http::post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $targetChatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['ok']) && $result['ok'] === true) {
                    return ['success' => true, 'error' => null];
                }
            }

            // Get error details
            $errorResponse = $response->json();
            $errorDescription = $errorResponse['description'] ?? 'Unknown error';
            $errorCode = $errorResponse['error_code'] ?? $response->status();

            // Provide user-friendly error messages
            $userFriendlyError = $this->getUserFriendlyError($errorCode, $errorDescription, $targetChatId);

            Log::error('Telegram API error', [
                'response' => $errorResponse,
                'status' => $response->status(),
                'chat_id' => $targetChatId,
            ]);

            return [
                'success' => false,
                'error' => $userFriendlyError
            ];
        } catch (\Exception $e) {
            Log::error('Telegram send message exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'chat_id' => $chatId ?? $this->chatId,
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user-friendly error message
     */
    private function getUserFriendlyError(int $errorCode, string $description, string $chatId): string
    {
        switch ($errorCode) {
            case 400:
                if (str_contains($description, 'chat not found')) {
                    return "Chat not found. The user with ID '{$chatId}' may not have started a conversation with your bot. Please ensure:\n\n1. The user has sent at least one message to your bot (@YourBotUsername)\n2. The Telegram ID is correct (username or numeric ID)\n3. If using username, make sure it includes '@' symbol\n\nAsk the user to start a chat with your bot first.";
                }
                if (str_contains($description, 'chat_id is empty')) {
                    return "Invalid chat ID. Please provide a valid Telegram ID.";
                }
                if (str_contains($description, 'message is too long')) {
                    return "Message is too long. Telegram messages have a maximum length limit.";
                }
                return "Bad Request: {$description}";
            
            case 403:
                return "Forbidden: The bot is blocked by the user or doesn't have permission to send messages. The user needs to unblock the bot or allow messages.";
            
            case 404:
                return "Bot not found. Please check your bot token in the configuration.";
            
            case 429:
                return "Rate limit exceeded. Please wait a moment before sending another message.";
            
            default:
                return "Error ({$errorCode}): {$description}";
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
            "⚠️⚠️⚠️ <b>Event Interrupted</b> ⚠️⚠️⚠️",
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
        $lines[] = "<b>Reminder set for " . ($event->remind_me_after ?? 0) . " minutes.</b>";
        $lines[] = "";
        $lines[] = "<b>I have set a 0–1 limit in the market for the event mentioned above.</b>";

        return implode("\n", $lines);
    }

    /**
     * Send an immediate notification when event is first interrupted
     *
     * @param object $event
     * @return bool
     */
    public function sendInterruptionNotification($event): bool
    {
        $message = $this->formatInterruptionNotificationMessage($event);
        return $this->sendMessage($message);
    }

    /**
     * Format immediate interruption notification message
     *
     * @param object $event
     * @return string
     */
    protected function formatInterruptionNotificationMessage($event): string
    {
        $lines = [
            "⚠️⚠️⚠️ <b>Event Interrupted</b> ⚠️⚠️⚠️",
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

        if (!empty($event->remind_me_after)) {
            $lines[] = "";
            $lines[] = "<b>Reminder scheduled for " . ($event->remind_me_after ?? 0) . " minutes.</b>";
        }

        $lines[] = "";
        $lines[] = "<b>I have set a 0–1 limit in the market for the event mentioned above.</b>";

        return implode("\n", $lines);
    }

    /**
     * Send notification when event interruption is turned OFF
     *
     * @param object $event
     * @return bool
     */
    public function sendInterruptionResolvedNotification($event): bool
    {
        $message = $this->formatInterruptionResolvedMessage($event);
        return $this->sendMessage($message);
    }

    /**
     * Format interruption resolved notification message
     *
     * @param object $event
     * @return string
     */
    protected function formatInterruptionResolvedMessage($event): string
    {
        $lines = [
            "✅✅✅ <b>Now Bats Open</b> ✅✅✅",
            "",
            "<b>Event:</b> " . ($event->eventName ?? 'N/A'),
            "<b>Sport:</b> " . ($event->sportName ?? 'N/A'),
        ];

        if (!empty($event->market_old_limits) && is_array($event->market_old_limits)) {
            $lines[] = "";
            $lines[] = "<b>Current Market Limits:</b>";
            foreach ($event->market_old_limits as $market) {
                $lines[] = "  • " . ($market->marketName ?? 'N/A') . ": " . ($market->old_limit ?? 0);
            }
        }

        $lines[] = "";
        $lines[] = "<b>The interruption for this event has been resolved.</b>";

        return implode("\n", $lines);
    }
}

