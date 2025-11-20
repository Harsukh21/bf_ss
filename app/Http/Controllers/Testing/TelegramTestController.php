<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TelegramService;
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

            // Send the test message with detailed error information
            $result = $this->telegramService->sendMessageWithDetails($message, $telegramId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => '✅ Test message sent successfully to ' . $telegramId . '!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Failed to send message. Please check the logs for details.'
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
