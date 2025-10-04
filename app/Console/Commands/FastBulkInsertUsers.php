<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FastBulkInsertUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fast-bulk-insert {count=6000 : Number of users to insert}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ultra-fast bulk insert users using raw SQL for maximum performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $this->info("Starting ultra-fast bulk insert of {$count} users...");
        
        $startTime = microtime(true);
        
        // Disable query logging for maximum performance
        DB::disableQueryLog();
        
        // Start transaction for better performance
        DB::beginTransaction();
        
        try {
            $hashedPassword = Hash::make('password');
            $now = now();
            
            // Use large batch inserts for maximum speed
            $batchSize = 2000; // Large batches for PostgreSQL
            $batches = ceil($count / $batchSize);
            $insertedCount = 0;
            
            for ($batch = 0; $batch < $batches; $batch++) {
                $currentBatchSize = min($batchSize, $count - $insertedCount);
                $batchData = [];
                
                // Generate batch data
                for ($i = 0; $i < $currentBatchSize; $i++) {
                    $userNum = $insertedCount + $i + 1;
                    $batchData[] = [
                        'name' => "User {$userNum}",
                        'email' => "user{$userNum}@example.com",
                        'email_verified_at' => $now,
                        'password' => $hashedPassword,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                
                // Bulk insert the batch
                DB::table('users')->insert($batchData);
                $insertedCount += $currentBatchSize;
                
                // Progress indicator
                $progress = round(($insertedCount / $count) * 100, 1);
                $this->info("Progress: {$insertedCount}/{$count} ({$progress}%)");
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 3);
        
        $this->info("✅ Successfully inserted {$count} users in {$executionTime} seconds");
        $this->info("📊 Performance: " . round($count / $executionTime, 0) . " records per second");
        
        // Verify the count
        $actualCount = DB::table('users')->count();
        $this->info("📋 Total users in database: {$actualCount}");
        
        return Command::SUCCESS;
    }
}
