<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use App\Services\TelegramUpdateService;
use Illuminate\Support\Facades\Log;

class TelegramTestController extends Controller
{
    protected $telegramService;

    public function __construct()
    {
        $this->telegramService = new TelegramService();
    }

    /**
     * Display the Telegram test page.
     */
    public function index()
    {
        return view('testing.telegram.index');
    }

    /**
     * Send a test Telegram message.
     */
    public function sendTestMessage(Request $request)
    {
        $request->validate([
            'telegram_id' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            $telegramId = trim($request->input('telegram_id'));
            $message = $request->input('message');

            // First, try to sync updates to get latest chat_id mappings
            $updateService = new TelegramUpdateService();
            try {
                $updateService->syncUpdates();
            } catch (\Exception $e) {
                // Continue even if sync fails - we'll try to find existing chat_id
                Log::debug('Telegram sync failed during test, continuing', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Get chat_id for the provided telegram_id
            $chatId = $updateService->getChatIdByTelegramId($telegramId);
            
            // Determine target ID - prefer chat_id if available, otherwise use telegram_id
            $targetId = $chatId ? (string)$chatId : $telegramId;
            
            // Send the test message with detailed error information
            $result = $this->telegramService->sendMessageWithDetails($message, $targetId);

            if ($result['success']) {
                $successMessage = '✅ Test message sent successfully to ' . $telegramId;
                if ($chatId && $telegramId !== (string)$chatId) {
                    $successMessage .= ' (using chat ID: ' . $chatId . ')';
                }
                $successMessage .= '!';
                
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            } else {
                // Provide helpful error message
                $errorMessage = $result['error'] ?? 'Failed to send message. Please check the logs for details.';
                
                // If chat_id not found, provide specific guidance
                if (!$chatId && !is_numeric($telegramId)) {
                    $errorMessage = "Chat ID not found for '{$telegramId}'. Please ensure:\n\n" .
                                   "1. The user has sent a message to the bot first\n" .
                                   "2. The Telegram username is correct\n" .
                                   "3. Try using the numeric chat ID instead (e.g., 5566325908)";
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Telegram test message error', [
                'error' => $e->getMessage(),
                'telegram_id' => $request->input('telegram_id'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
