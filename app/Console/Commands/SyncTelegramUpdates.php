<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramUpdateService;

class SyncTelegramUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:sync-updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Telegram updates and store chat_id mappings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Telegram updates sync...');
        
        $service = new TelegramUpdateService();
        $result = $service->syncUpdates();
        
        if ($result['success']) {
            $this->info("✅ Successfully processed {$result['processed']} chat(s)");
            if ($result['total_updates'] > 0) {
                $this->info("Total updates received: {$result['total_updates']}");
            }
        } else {
            $this->error("❌ Error: {$result['message']}");
            return 1;
        }
        
        return 0;
    }
}
