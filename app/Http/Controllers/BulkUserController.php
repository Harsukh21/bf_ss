<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BulkUserController extends Controller
{
    /**
     * Show the bulk user insertion form
     */
    public function index()
    {
        $userCount = DB::table('users')->count();
        return view('bulk-users', compact('userCount'));
    }

    /**
     * Handle bulk user insertion
     */
    public function store(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:50000',
            'method' => 'required|in:fast,regular'
        ]);

        $count = $request->input('count');
        $method = $request->input('method');
        
        $startTime = microtime(true);
        
        try {
            // Disable query logging for performance
            DB::disableQueryLog();
            
            // Start transaction
            DB::beginTransaction();
            
            if ($method === 'fast') {
                $this->fastBulkInsert($count);
            } else {
                $this->regularBulkInsert($count);
            }
            
            DB::commit();
            
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 3);
            $recordsPerSecond = round($count / $executionTime, 0);
            
            return redirect()->back()->with('success', [
                'message' => "Successfully inserted {$count} users in {$executionTime} seconds",
                'performance' => "{$recordsPerSecond} records per second",
                'method' => $method
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error inserting users: ' . $e->getMessage());
        }
    }

    /**
     * Fast bulk insert method
     */
    private function fastBulkInsert($count)
    {
        $hashedPassword = Hash::make('password');
        $now = now();
        $timestamp = time(); // Add timestamp to ensure uniqueness
        
        // Use large batch inserts for maximum speed
        $batchSize = 2000;
        $batches = ceil($count / $batchSize);
        $insertedCount = 0;
        
        for ($batch = 0; $batch < $batches; $batch++) {
            $currentBatchSize = min($batchSize, $count - $insertedCount);
            $batchData = [];
            
            // Generate batch data with unique emails
            for ($i = 0; $i < $currentBatchSize; $i++) {
                $userNum = $insertedCount + $i + 1;
                $batchData[] = [
                    'name' => "User {$userNum}",
                    'email' => "user{$userNum}_{$timestamp}@example.com", // Add timestamp for uniqueness
                    'email_verified_at' => $now,
                    'password' => $hashedPassword,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            
            // Bulk insert the batch
            DB::table('users')->insert($batchData);
            $insertedCount += $currentBatchSize;
        }
    }

    /**
     * Regular bulk insert method with Faker
     */
    private function regularBulkInsert($count)
    {
        $faker = \Faker\Factory::create();
        $timestamp = time(); // Add timestamp to ensure uniqueness
        
        // Prepare batch data
        $batchSize = 1000;
        $batches = ceil($count / $batchSize);
        $insertedCount = 0;
        
        for ($batch = 0; $batch < $batches; $batch++) {
            $currentBatchSize = min($batchSize, $count - $insertedCount);
            $batchData = [];
            
            for ($i = 0; $i < $currentBatchSize; $i++) {
                // Generate unique email with timestamp and random suffix
                $email = "user" . ($insertedCount + $i + 1) . "_{$timestamp}_" . uniqid() . "@example.com";
                $batchData[] = [
                    'name' => $faker->name(),
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Bulk insert the batch
            DB::table('users')->insert($batchData);
            $insertedCount += $currentBatchSize;
        }
    }

    /**
     * Clear all users from database
     */
    public function clear()
    {
        try {
            DB::table('users')->truncate();
            return redirect()->back()->with('success', [
                'message' => 'All users have been cleared from the database',
                'performance' => null,
                'method' => 'clear'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error clearing users: ' . $e->getMessage());
        }
    }
}
