<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SingleInsertController extends Controller
{
    public function index()
    {
        // Get total user count (cached for performance)
        $userCount = Cache::remember('total_users_count', 300, function () {
            return DB::table('users')->count();
        });

        // Load only first 50 records for initial page load (much faster)
        $page = request()->get('page', 1);
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        // Use raw SQL for maximum speed - only load what's needed
        $sql = "SELECT id, name, first_name, last_name, email, gender, country, industry, status, created_at 
                FROM users 
                ORDER BY id DESC 
                LIMIT 50 OFFSET ?";

        $latestRecords = collect(DB::select($sql, [$offset]));

        $latestUsers = new \Illuminate\Pagination\LengthAwarePaginator(
            $latestRecords,
            min($userCount, 1000), // Show pagination for 1000 records
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('single-insert.index', compact('latestUsers', 'userCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:50000'
        ]);

        $count = $request->count;
        $method = $request->method ?? 'single';

        $startTime = microtime(true);

        // Disable query logging for performance
        DB::disableQueryLog();

        try {
            DB::beginTransaction();

            // Basic database optimizations
            try {
                DB::statement('SET synchronous_commit = off');
            } catch (\Exception $e) {}

            $result = $this->singleInsert($count);

            DB::commit();

            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 3);

            $message = "Successfully inserted {$count} users using single-record inserts in {$executionTime} seconds!";
            $message .= " Average: " . round($executionTime / $count * 1000, 2) . "ms per record.";

            return redirect()->back()->with([
                'success' => $message,
                'execution_time' => $executionTime,
                'records_inserted' => $count,
                'method' => 'Single Record Insert'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error inserting users: ' . $e->getMessage());
        }
    }

    private function singleInsert($count)
    {
        $inserted = 0;
        $batchSize = 1000; // Process in batches of 1000 for memory management

        // Pre-defined data arrays for speed
        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Lisa', 'Robert', 'Emily', 'James', 'Jessica'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];
        $countries = ['United States', 'Canada', 'United Kingdom', 'Germany', 'France', 'Australia', 'Japan', 'Brazil', 'India', 'China'];
        $industries = ['Technology', 'Healthcare', 'Finance', 'Education', 'Retail', 'Manufacturing', 'Real Estate', 'Transportation', 'Energy', 'Media'];
        $genders = ['male', 'female', 'other', 'prefer_not_to_say'];

        // Pre-hash password for speed
        $hashedPassword = Hash::make('password123');

        $currentTime = now();
        $baseTimestamp = time();

        for ($i = 0; $i < $count; $i += $batchSize) {
            $batchCount = min($batchSize, $count - $i);
            $batchData = [];

            for ($j = 0; $j < $batchCount; $j++) {
                $recordNum = $i + $j + 1;
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                
                $batchData[] = [
                    'name' => "{$firstName} {$lastName}",
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => "user{$recordNum}_{$baseTimestamp}@example.com",
                    'password' => $hashedPassword,
                    'gender' => $genders[array_rand($genders)],
                    'country' => $countries[array_rand($countries)],
                    'industry' => $industries[array_rand($industries)],
                    'status' => 'active',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];
            }

            // Single record insert loop
            foreach ($batchData as $record) {
                DB::table('users')->insert($record);
                $inserted++;
            }

            // Small delay to prevent memory issues
            if ($i + $batchSize < $count) {
                usleep(1000); // 1ms delay
            }
        }

        return $inserted;
    }

    public function getLatestRecords(Request $request)
    {
        // Disable query logging for performance
        DB::disableQueryLog();

        $page = $request->get('page', 1);
        $perPage = 100;
        $offset = ($page - 1) * $perPage;

        // Get latest 100 records using raw SQL for speed
        $sql = "SELECT id, name, first_name, last_name, email, gender, country, industry, status, created_at 
                FROM users 
                ORDER BY id DESC 
                LIMIT 100 OFFSET ?";

        $latestRecords = collect(DB::select($sql, [$offset]));

        // Get total count for latest 100 records (cached)
        $totalCount = Cache::remember('latest_100_count', 60, function () {
            return DB::table('users')->count();
        });

        $latestUsers = new \Illuminate\Pagination\LengthAwarePaginator(
            $latestRecords,
            min($totalCount, 1000),
            $perPage,
            $page,
            ['path' => route('single-insert.latest-records'), 'query' => request()->query()]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $latestUsers->items(),
                'pagination' => [
                    'current_page' => $latestUsers->currentPage(),
                    'last_page' => $latestUsers->lastPage(),
                    'per_page' => $latestUsers->perPage(),
                    'total' => $latestUsers->total(),
                    'has_more_pages' => $latestUsers->hasMorePages()
                ]
            ]
        ]);
    }
}
