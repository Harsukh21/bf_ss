<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class BulkInsertUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:bulk-insert {count=6000 : Number of users to insert}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk insert users into the database for performance testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $this->info("Starting bulk insert of {$count} users...");
        
        $startTime = microtime(true);
        
        // Disable query logging for performance
        DB::disableQueryLog();
        
        // Use Faker for generating realistic data
        $faker = Faker::create();
        
        // Prepare batch data
        $batchSize = 1000; // Insert 1000 records at a time
        $batches = ceil($count / $batchSize);
        $insertedCount = 0;
        
        for ($batch = 0; $batch < $batches; $batch++) {
            $currentBatchSize = min($batchSize, $count - $insertedCount);
            $batchData = [];
            
            for ($i = 0; $i < $currentBatchSize; $i++) {
                // Generate unique email with timestamp and random suffix
                $email = "user" . ($insertedCount + $i + 1) . "_" . time() . "_" . uniqid() . "@example.com";
                $batchData[] = [
                    'name' => $faker->name(),
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'), // Default password
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Bulk insert the batch
            DB::table('users')->insert($batchData);
            $insertedCount += $currentBatchSize;
            
            // Progress indicator
            $progress = round(($insertedCount / $count) * 100, 1);
            $this->info("Progress: {$insertedCount}/{$count} ({$progress}%)");
        }
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 3);
        
        $this->info("✅ Successfully inserted {$insertedCount} users in {$executionTime} seconds");
        $this->info("📊 Performance: " . round($insertedCount / $executionTime, 0) . " records per second");
        
        // Verify the count
        $actualCount = DB::table('users')->count();
        $this->info("📋 Total users in database: {$actualCount}");
        
        return Command::SUCCESS;
    }
}

