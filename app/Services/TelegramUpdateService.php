<?php

namespace App\Services;

use App\Models\TelegramChat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TelegramUpdateService
{
    protected $botToken;
    protected $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Get updates from Telegram API and store chat_id mappings
     *
     * @return array
     */
    public function syncUpdates(): array
    {
        if (empty($this->botToken)) {
            Log::warning('Telegram bot token not configured');
            return [
                'success' => false,
                'message' => 'Telegram bot token not configured',
                'processed' => 0
            ];
        }

        try {
            // Get the last processed update_id from database
            $lastUpdateId = TelegramChat::max('update_id') ?? 0;

            // Fetch updates from Telegram API with shorter timeout to avoid blocking
            // Use timeout=1 for quick response when there are no new updates
            $response = Http::timeout(10)->get("{$this->apiUrl}/getUpdates", [
                'offset' => $lastUpdateId + 1,
                'timeout' => 1, // Short timeout - Telegram will return immediately if no new updates
                'limit' => 100, // Limit number of updates per request
            ]);

            // Handle timeout gracefully - this is normal when there are no new updates
            if (!$response->successful()) {
                $error = $response->json();
                $statusCode = $response->status();
                
                // Check if it's a timeout - this is expected when there are no new updates
                if (str_contains($response->body() ?? '', 'timeout') || $statusCode === 0) {
                    // No new updates available - this is normal, not an error
                    return [
                        'success' => true,
                        'message' => 'No new updates available',
                        'processed' => 0,
                        'total_updates' => 0,
                    ];
                }
                
                Log::error('Telegram API error while fetching updates', [
                    'response' => $error,
                    'status' => $statusCode,
                ]);
                return [
                    'success' => false,
                    'message' => 'Failed to fetch updates from Telegram API',
                    'processed' => 0
                ];
            }

            $result = $response->json();

            if (!isset($result['ok']) || !$result['ok']) {
                $errorDescription = $result['description'] ?? 'Unknown error';
                return [
                    'success' => false,
                    'message' => 'Telegram API returned error: ' . $errorDescription,
                    'processed' => 0
                ];
            }

            $updates = $result['result'] ?? [];
            $processed = 0;

            foreach ($updates as $update) {
                if (isset($update['message']) && isset($update['message']['chat'])) {
                    $chat = $update['message']['chat'];
                    $from = $update['message']['from'] ?? [];

                    // Only process private chats (type === 'private')
                    if ($chat['type'] === 'private' && !($from['is_bot'] ?? false)) {
                        $chatId = $chat['id'];
                        $username = $chat['username'] ?? null;
                        $firstName = $chat['first_name'] ?? null;
                        $lastName = $chat['last_name'] ?? null;

                        // Store or update chat mapping
                        TelegramChat::updateOrCreate(
                            ['chat_id' => $chatId],
                            [
                                'telegram_username' => $username,
                                'first_name' => $firstName,
                                'last_name' => $lastName,
                                'is_bot' => $from['is_bot'] ?? false,
                                'language_code' => $from['language_code'] ?? null,
                                'last_message_at' => isset($update['message']['date']) 
                                    ? Carbon::createFromTimestamp($update['message']['date'])
                                    : now(),
                                'update_id' => $update['update_id'],
                            ]
                        );

                        $processed++;
                    }
                }
            }

            if ($processed > 0) {
                Log::info('Telegram updates synced successfully', [
                    'processed' => $processed,
                    'total_updates' => count($updates),
                ]);
            }

            return [
                'success' => true,
                'message' => $processed > 0 
                    ? "Successfully processed {$processed} chat(s)" 
                    : 'No new updates to process',
                'processed' => $processed,
                'total_updates' => count($updates),
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Connection timeout is expected when there are no new updates
            if (str_contains($e->getMessage(), 'timeout')) {
                return [
                    'success' => true,
                    'message' => 'No new updates available (timeout expected)',
                    'processed' => 0,
                    'total_updates' => 0,
                ];
            }
            
            Log::warning('Telegram sync connection exception', [
                'message' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
                'processed' => 0
            ];
        } catch (\Exception $e) {
            Log::error('Telegram update sync exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'processed' => 0
            ];
        }
    }

    /**
     * Get chat_id by telegram username
     *
     * @param string $telegramId Telegram username or chat_id
     * @return int|null
     */
    public function getChatIdByTelegramId(string $telegramId): ?int
    {
        // If it's already a numeric ID, return it
        if (is_numeric($telegramId)) {
            $chatId = (int) $telegramId;
            // Verify it exists in our database
            if (TelegramChat::findByChatId($chatId)) {
                return $chatId;
            }
            return null;
        }

        // If it's a username, look it up
        $cleanUsername = ltrim($telegramId, '@');
        $telegramChat = TelegramChat::findByUsername($cleanUsername);

        return $telegramChat ? $telegramChat->chat_id : null;
    }

    /**
     * Check if telegram_id has a valid chat_id mapping
     *
     * @param string $telegramId
     * @return bool
     */
    public function hasValidChatId(string $telegramId): bool
    {
        return $this->getChatIdByTelegramId($telegramId) !== null;
    }
}

