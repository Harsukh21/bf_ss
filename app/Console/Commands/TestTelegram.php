<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class TestTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Telegram bot connection and send a test message';

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
        $this->info('Testing Telegram connection...');
        $this->newLine();

        // Check configuration
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (empty($botToken)) {
            $this->error('❌ TELEGRAM_BOT_TOKEN is not set in .env file');
            return 1;
        }

        if (empty($chatId)) {
            $this->error('❌ TELEGRAM_CHAT_ID is not set in .env file');
            $this->newLine();
            $this->info('To get your Chat ID:');
            $this->line('  1. Search for @userinfobot on Telegram');
            $this->line('  2. Start a conversation and it will show your Chat ID');
            $this->line('  3. Or add your bot to a group and visit:');
            $this->line('     https://api.telegram.org/bot' . $botToken . '/getUpdates');
            return 1;
        }

        $this->info('✅ Bot Token: ' . substr($botToken, 0, 10) . '...');
        $this->info('✅ Chat ID: ' . $chatId);
        $this->newLine();

        // Send test message
        $this->info('Sending test message...');
        $testMessage = "🧪 <b>Test Message</b>\n\nThis is a test message from your Laravel application.\n\nIf you received this, your Telegram integration is working correctly! ✅";

        $success = $this->telegramService->sendMessage($testMessage);

        if ($success) {
            $this->info('✅ Test message sent successfully!');
            $this->info('Check your Telegram chat to verify.');
            return 0;
        } else {
            $this->error('❌ Failed to send test message');
            $this->error('Please check:');
            $this->line('  1. Bot token is correct');
            $this->line('  2. Chat ID is correct');
            $this->line('  3. Bot has permission to send messages to the chat');
            $this->line('  4. Check logs: storage/logs/laravel.log');
            return 1;
        }
    }
}

