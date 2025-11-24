<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HashWebPins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'web-pins:hash {--dry-run : Show what would be hashed without actually hashing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash all existing web_pins in the database for security';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('Starting web_pin hashing process...');
        
        // Get all users with web_pin set
        $users = DB::table('users')
            ->whereNotNull('web_pin')
            ->where('web_pin', '!=', '')
            ->get(['id', 'email', 'web_pin']);

        if ($users->isEmpty()) {
            $this->info('No users with web_pin found.');
            return 0;
        }

        $this->info("Found {$users->count()} user(s) with web_pin.");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made.');
        }

        $hashedCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            $webPin = $user->web_pin;
            
            // Check if already hashed (hashed passwords start with $2y$)
            if (strlen($webPin) >= 60 && (strpos($webPin, '$2y$') === 0 || strpos($webPin, '$2a$') === 0 || strpos($webPin, '$2b$') === 0)) {
                $this->line("User {$user->email} (ID: {$user->id}): Already hashed - skipping");
                $skippedCount++;
                continue;
            }

            // Validate it's a numeric PIN (6+ digits)
            if (!preg_match('/^[0-9]{6,}$/', $webPin)) {
                $this->warn("User {$user->email} (ID: {$user->id}): Invalid PIN format - skipping");
                $skippedCount++;
                continue;
            }

            if ($dryRun) {
                $this->line("Would hash web_pin for user: {$user->email} (ID: {$user->id})");
                $hashedCount++;
            } else {
                // Hash the web_pin
                $hashedPin = Hash::make($webPin);
                
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['web_pin' => $hashedPin]);
                
                $this->info("✓ Hashed web_pin for user: {$user->email} (ID: {$user->id})");
                $hashedCount++;
            }
        }

        $this->newLine();
        $this->info('Summary:');
        $this->info("  Total users with web_pin: {$users->count()}");
        $this->info("  {$hashedCount} web_pin(s) " . ($dryRun ? 'would be hashed' : 'hashed'));
        $this->info("  {$skippedCount} web_pin(s) skipped (already hashed or invalid)");

        if (!$dryRun) {
            $this->newLine();
            $this->info('✅ All web_pins have been hashed successfully!');
            $this->warn('⚠️  IMPORTANT: Users will need to use their original web_pin for login - it will be verified against the hash.');
        }

        return 0;
    }
}
