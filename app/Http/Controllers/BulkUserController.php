<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class BulkUserController extends Controller
{
    /**
     * Show the bulk user insertion form with latest 1000 records only
     */
    public function index()
    {
        try {
            $userCount = DB::table('users')->count();
            
            // Get only the latest 1000 records with pagination (limit to first 1000)
            // Using a subquery to limit the dataset first, then paginate
            $latestUsers = DB::table('users')
                ->select('*')
                ->orderBy('id', 'desc')
                ->limit(1000)
                ->get()
                ->chunk(50); // Manually chunk into pages of 50
            
            // Convert to Laravel paginator for the view
            $page = request()->get('page', 1);
            $perPage = 50;
            $offset = ($page - 1) * $perPage;
            
            $latestRecords = DB::table('users')
                ->select([
                    'id', 'name', 'first_name', 'last_name', 'email',
                    'gender', 'country', 'industry', 'status', 'created_at'
                ])
                ->orderBy('id', 'desc')
                ->limit(1000)
                ->get();
            
            $latestUsers = new \Illuminate\Pagination\LengthAwarePaginator(
                $latestRecords->slice($offset, $perPage)->values(),
                min($latestRecords->count(), 1000),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
                
        } catch (\Exception $e) {
            $userCount = 0;
            $latestUsers = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 50);
        }
        
        return view('bulk-users', compact('userCount', 'latestUsers'));
    }

    /**
     * Handle bulk user insertion
     */
    public function store(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:50000',
            'method' => 'required|in:fast,regular,ultra'
        ]);

        $count = $request->input('count');
        $method = $request->input('method');
        
        $startTime = microtime(true);
        
        try {
            // Disable query logging for performance
            DB::disableQueryLog();
            
            // Optimize database connection for bulk operations (PostgreSQL)
            // Use try-catch for each parameter to handle version differences gracefully
            try {
                DB::statement('SET synchronous_commit = off');
            } catch (\Exception $e) {
                // Ignore if parameter not supported
            }
            
            try {
                DB::statement('SET maintenance_work_mem = 256MB');
            } catch (\Exception $e) {
                // Ignore if parameter not supported
            }
            
            try {
                DB::statement('SET checkpoint_completion_target = 0.9');
            } catch (\Exception $e) {
                // Ignore if parameter not supported
            }
            
            try {
                DB::statement('SET max_wal_size = 2GB');
            } catch (\Exception $e) {
                // Fallback for older PostgreSQL versions
                try {
                    DB::statement('SET wal_buffers = 16MB');
                } catch (\Exception $e2) {
                    // Ignore if parameter not supported
                }
            }
            
            // Start transaction
            DB::beginTransaction();
            
            if ($method === 'ultra') {
                $this->ultraFastBulkInsert($count);
            } elseif ($method === 'fast') {
                $this->fastBulkInsert($count);
            } else {
                $this->regularBulkInsert($count);
            }
            
            DB::commit();
            
            // Restore database settings (PostgreSQL)
            try {
                DB::statement('SET synchronous_commit = on');
            } catch (\Exception $e) {
                // Ignore if parameter not supported
            }
            
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 3);
            $recordsPerSecond = round($count / $executionTime, 0);
            
            return redirect()->back()->with('success', [
                'message' => "Successfully inserted {$count} users in {$executionTime} seconds",
                'performance' => "{$recordsPerSecond} records per second",
                'method' => $method,
                'count' => $count,
                'time' => $executionTime,
                'records_per_second' => $recordsPerSecond
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            // Restore database settings even on error (PostgreSQL)
            try {
                DB::statement('SET synchronous_commit = on');
            } catch (\Exception $restoreError) {
                // Log but don't fail the main error - this is expected for some PostgreSQL versions
                \Log::info('Could not restore synchronous_commit setting: ' . $restoreError->getMessage());
            }
            
            return redirect()->back()->with('error', 'Error inserting users: ' . $e->getMessage());
        }
    }

    /**
     * Fast bulk insert method - Optimized for maximum speed
     */
    private function fastBulkInsert($count)
    {
        // Pre-hash password once
        $hashedPassword = Hash::make('password');
        $now = now()->toDateTimeString();
        $timestamp = time();
        
        // Use much larger batch size for better performance
        $batchSize = 5000;
        $batches = ceil($count / $batchSize);
        $insertedCount = 0;
        
        // Pre-defined data arrays for speed
        $genders = ['male', 'female', 'other'];
        $industries = ['Technology', 'Healthcare', 'Finance', 'Education', 'Retail', 'Manufacturing'];
        $countries = ['United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Japan'];
        $cities = ['New York', 'London', 'Tokyo', 'Sydney', 'Berlin', 'Paris', 'Toronto'];
        
        for ($batch = 0; $batch < $batches; $batch++) {
            $currentBatchSize = min($batchSize, $count - $insertedCount);
            
            // Use raw SQL with VALUES for maximum performance
            $values = [];
            for ($i = 0; $i < $currentBatchSize; $i++) {
                $userNum = $insertedCount + $i + 1;
                $gender = $genders[$userNum % 3];
                $industry = $industries[$userNum % 6];
                $country = $countries[$userNum % 7];
                $city = $cities[$userNum % 7];
                
                $values[] = "(
                    'User {$userNum}',
                    'User',
                    '{$userNum}',
                    'user{$userNum}_{$timestamp}@example.com',
                    '{$now}',
                    '{$hashedPassword}',
                    '1990-01-01',
                    '{$gender}',
                    '+1234567890',
                    '{$country}',
                    '{$city}',
                    'State',
                    '12345',
                    '123 Main St',
                    'Software Developer',
                    'Tech Corp',
                    '{$industry}',
                    " . rand(40000, 120000) . ",
                    'Professional software developer',
                    'https://example.com',
                    'https://linkedin.com/in/user{$userNum}',
                    '@user{$userNum}',
                    'active',
                    true,
                    false,
                    null,
                    'Bulk inserted user #{$userNum}',
                    '{$now}',
                    '{$now}'
                )";
            }
            
            // Raw SQL insert for maximum speed
            $sql = "INSERT INTO users (
                name, first_name, last_name, email, email_verified_at, password, 
                date_of_birth, gender, phone, country, city, state, postal_code, 
                address, job_title, company, industry, salary, bio, website, 
                linkedin_url, twitter_handle, status, email_notifications, 
                sms_notifications, avatar, notes, created_at, updated_at
            ) VALUES " . implode(',', $values);
            
            DB::statement($sql);
            $insertedCount += $currentBatchSize;
        }
    }

    /**
     * Ultra-fast bulk insert method using optimized raw SQL with VALUES
     */
    private function ultraFastBulkInsert($count)
    {
        // Pre-hash password once
        $hashedPassword = Hash::make('password');
        $now = now()->toDateTimeString();
        $timestamp = time();
        
        // Pre-defined data arrays for speed
        $genders = ['male', 'female', 'other'];
        $industries = ['Technology', 'Healthcare', 'Finance', 'Education', 'Retail', 'Manufacturing'];
        $countries = ['United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'Japan'];
        $cities = ['New York', 'London', 'Tokyo', 'Sydney', 'Berlin', 'Paris', 'Toronto'];
        
        // Use very large batch size for maximum performance (no file system dependency)
        $batchSize = 10000; // Even larger batch for ultra-fast method
        $batches = ceil($count / $batchSize);
        $insertedCount = 0;
        
        for ($batch = 0; $batch < $batches; $batch++) {
            $currentBatchSize = min($batchSize, $count - $insertedCount);
            
            // Use raw SQL with VALUES for maximum performance
            $values = [];
            for ($i = 0; $i < $currentBatchSize; $i++) {
                $userNum = $insertedCount + $i + 1;
                $gender = $genders[$userNum % 3];
                $industry = $industries[$userNum % 6];
                $country = $countries[$userNum % 7];
                $city = $cities[$userNum % 7];
                
                $values[] = "(
                    'User {$userNum}',
                    'User',
                    '{$userNum}',
                    'user{$userNum}_{$timestamp}@example.com',
                    '{$now}',
                    '{$hashedPassword}',
                    '1990-01-01',
                    '{$gender}',
                    '+1234567890',
                    '{$country}',
                    '{$city}',
                    'State',
                    '12345',
                    '123 Main St',
                    'Software Developer',
                    'Tech Corp',
                    '{$industry}',
                    " . rand(40000, 120000) . ",
                    'Professional software developer',
                    'https://example.com',
                    'https://linkedin.com/in/user{$userNum}',
                    '@user{$userNum}',
                    'active',
                    true,
                    false,
                    null,
                    'Bulk inserted user #{$userNum}',
                    '{$now}',
                    '{$now}'
                )";
            }
            
            // Raw SQL insert for maximum speed (no file system dependency)
            $sql = "INSERT INTO users (
                name, first_name, last_name, email, email_verified_at, password, 
                date_of_birth, gender, phone, country, city, state, postal_code, 
                address, job_title, company, industry, salary, bio, website, 
                linkedin_url, twitter_handle, status, email_notifications, 
                sms_notifications, avatar, notes, created_at, updated_at
            ) VALUES " . implode(',', $values);
            
            DB::statement($sql);
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
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'date_of_birth' => $faker->date('Y-m-d', '2000-01-01'),
                    'gender' => $faker->randomElement(['male', 'female', 'other']),
                    'phone' => $faker->phoneNumber(),
                    'country' => $faker->country(),
                    'city' => $faker->city(),
                    'state' => $faker->state(),
                    'postal_code' => $faker->postcode(),
                    'address' => $faker->address(),
                    'job_title' => $faker->jobTitle(),
                    'company' => $faker->company(),
                    'industry' => $faker->randomElement(['Technology', 'Healthcare', 'Finance', 'Education', 'Retail', 'Manufacturing']),
                    'salary' => $faker->numberBetween(30000, 150000),
                    'bio' => $faker->sentence(10),
                    'website' => $faker->url(),
                    'linkedin_url' => 'https://linkedin.com/in/' . $faker->userName(),
                    'twitter_handle' => '@' . $faker->userName(),
                    'status' => 'active',
                    'email_notifications' => true,
                    'sms_notifications' => $faker->boolean(),
                    'avatar' => null,
                    'notes' => 'Bulk inserted user with Faker data',
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
     * Get server resource information
     */
    public function getResources()
    {
        try {
            $resources = [
                'cpu_usage' => $this->getCpuUsage(),
                'memory_usage' => $this->getMemoryUsage(),
                'disk_usage' => $this->getDiskUsage(),
                'database_size' => $this->getDatabaseSize(),
                'active_connections' => $this->getActiveConnections(),
                'timestamp' => now()->format('H:i:s'),
                'uptime' => $this->getUptime(),
            ];

            return response()->json($resources);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get CPU usage percentage
     */
    private function getCpuUsage()
    {
        try {
            // Method 1: Use /proc/loadavg for more accurate results
            $load = sys_getloadavg();
            $cpuCount = $this->getCpuCount();
            
            if ($cpuCount > 0) {
                // Convert 1-minute load average to approximate CPU percentage
                $cpuPercent = ($load[0] / $cpuCount) * 100;
                return round(min($cpuPercent, 100), 1);
            }
            
            // Method 2: Fallback to /proc/stat (improved calculation)
            $stat1 = file_get_contents('/proc/stat');
            usleep(200000); // Wait 0.2 seconds for better accuracy
            $stat2 = file_get_contents('/proc/stat');
            
            $info1 = explode("\n", $stat1);
            $info2 = explode("\n", $stat2);
            
            if (empty($info1[0]) || empty($info2[0])) {
                return 0.0;
            }
            
            $cpu1 = preg_split('/\s+/', trim($info1[0]));
            $cpu2 = preg_split('/\s+/', trim($info2[0]));
            
            // Skip the 'cpu' label and get the numeric values
            $cpu1 = array_slice($cpu1, 1, 7);
            $cpu2 = array_slice($cpu2, 1, 7);
            
            $cpu1 = array_map('intval', $cpu1);
            $cpu2 = array_map('intval', $cpu2);
            
            // Calculate differences
            $dif = [];
            for ($i = 0; $i < 7; $i++) {
                $dif[$i] = $cpu2[$i] - $cpu1[$i];
            }
            
            // Calculate CPU usage
            $idle = $dif[3]; // idle time
            $total = array_sum($dif); // total time
            
            if ($total <= 0) {
                return 0.0;
            }
            
            $cpu = (($total - $idle) / $total) * 100;
            return round(min($cpu, 100), 1);
            
        } catch (\Exception $e) {
            // Final fallback: return 0 if all methods fail
            return 0.0;
        }
    }

    /**
     * Get CPU count
     */
    private function getCpuCount()
    {
        $cpuinfo = file_get_contents('/proc/cpuinfo');
        preg_match_all('/^processor/m', $cpuinfo, $matches);
        return count($matches[0]) ?: 1;
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage()
    {
        $meminfo = file_get_contents('/proc/meminfo');
        preg_match('/MemTotal:\s+(\d+)/', $meminfo, $total);
        preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $available);
        
        if (isset($total[1]) && isset($available[1])) {
            $used = $total[1] - $available[1];
            return [
                'used' => $this->formatBytes($used * 1024),
                'total' => $this->formatBytes($total[1] * 1024),
                'percentage' => round(($used / $total[1]) * 100, 1)
            ];
        }
        
        return ['used' => 'N/A', 'total' => 'N/A', 'percentage' => 0];
    }

    /**
     * Get disk usage
     */
    private function getDiskUsage()
    {
        $bytes = disk_free_space('/');
        $total = disk_total_space('/');
        $used = $total - $bytes;
        
        return [
            'used' => $this->formatBytes($used),
            'total' => $this->formatBytes($total),
            'percentage' => round(($used / $total) * 100, 1)
        ];
    }

    /**
     * Get database size
     */
    private function getDatabaseSize()
    {
        try {
            $size = DB::select("SELECT pg_size_pretty(pg_database_size(current_database())) as size")[0]->size;
            return $size;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get active database connections
     */
    private function getActiveConnections()
    {
        try {
            $count = DB::select("SELECT count(*) as count FROM pg_stat_activity WHERE state = 'active'")[0]->count;
            return (int)$count;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get system uptime
     */
    private function getUptime()
    {
        $uptime = shell_exec('uptime -p 2>/dev/null');
        return trim($uptime) ?: 'N/A';
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Test database connection and return detailed information
     */
    public function testDatabase()
    {
        $startTime = microtime(true);
        $testResults = [];
        $overallStatus = 'success';
        
        try {
            // Test basic connection
            $connectionStart = microtime(true);
            $connection = DB::connection();
            $connection->getPdo();
            $connectionTime = round((microtime(true) - $connectionStart) * 1000, 2);
            
            $testResults[] = [
                'test' => 'Database Connection',
                'status' => 'success',
                'message' => 'Successfully connected to PostgreSQL database',
                'time' => $connectionTime . 'ms',
                'details' => 'Connection established successfully'
            ];
            
            // Test basic query
            $queryStart = microtime(true);
            $result = DB::select('SELECT 1 as test_value');
            $queryTime = round((microtime(true) - $queryStart) * 1000, 2);
            
            $testResults[] = [
                'test' => 'Basic Query Test',
                'status' => 'success',
                'message' => 'Basic SELECT query executed successfully',
                'time' => $queryTime . 'ms',
                'details' => 'Query returned: ' . $result[0]->test_value
            ];
            
            // Test database info
            $infoStart = microtime(true);
            $dbInfo = DB::select("SELECT 
                current_database() as database_name,
                version() as version,
                current_user as user,
                inet_server_addr() as host,
                inet_server_port() as port
            ")[0];
            $infoTime = round((microtime(true) - $infoStart) * 1000, 2);
            
            $testResults[] = [
                'test' => 'Database Information',
                'status' => 'success',
                'message' => 'Database information retrieved successfully',
                'time' => $infoTime . 'ms',
                'details' => "Database: {$dbInfo->database_name}, User: {$dbInfo->user}"
            ];
            
            // Test table access
            $tableStart = microtime(true);
            $tableCount = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'public'")[0]->count;
            $tableTime = round((microtime(true) - $tableStart) * 1000, 2);
            
            $testResults[] = [
                'test' => 'Table Access Test',
                'status' => 'success',
                'message' => 'Table access verified successfully',
                'time' => $tableTime . 'ms',
                'details' => "Found {$tableCount} tables in public schema"
            ];
            
            // Test users table specifically
            $usersStart = microtime(true);
            $userCount = DB::table('users')->count();
            $usersTime = round((microtime(true) - $usersStart) * 1000, 2);
            
            // Get column count for users table
            $columnCount = count(DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'users' AND table_schema = 'public'"));
            
            $testResults[] = [
                'test' => 'Users Table Test',
                'status' => 'success',
                'message' => 'Users table access verified',
                'time' => $usersTime . 'ms',
                'details' => "Users table contains {$userCount} records with {$columnCount} fields"
            ];
            
            // Test transaction capability
            $transactionStart = microtime(true);
            DB::beginTransaction();
            DB::rollback(); // Rollback to test transaction capability
            $transactionTime = round((microtime(true) - $transactionStart) * 1000, 2);
            
            $testResults[] = [
                'test' => 'Transaction Test',
                'status' => 'success',
                'message' => 'Transaction support verified',
                'time' => $transactionTime . 'ms',
                'details' => 'Begin/rollback transaction executed successfully'
            ];
            
        } catch (\Exception $e) {
            $overallStatus = 'error';
            $testResults[] = [
                'test' => 'Database Connection Error',
                'status' => 'error',
                'message' => 'Database connection failed',
                'time' => 'N/A',
                'details' => $e->getMessage()
            ];
        }
        
        $totalTime = round((microtime(true) - $startTime) * 1000, 2);
        
        return view('database-test', compact('testResults', 'overallStatus', 'totalTime', 'dbInfo'));
    }

    /**
     * Get latest records via AJAX
     */
    public function getLatestRecords(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 50);
            $latestUsers = DB::table('users')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'users' => $latestUsers->items(),
                'pagination' => [
                    'current_page' => $latestUsers->currentPage(),
                    'last_page' => $latestUsers->lastPage(),
                    'per_page' => $latestUsers->perPage(),
                    'total' => $latestUsers->total(),
                    'from' => $latestUsers->firstItem(),
                    'to' => $latestUsers->lastItem()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading latest records: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display all users with optimized pagination for large datasets
     */
    public function viewUsers(Request $request)
    {
        $startTime = microtime(true);
        
        try {
            // Disable query logging for performance
            DB::disableQueryLog();
            
            // Get total count efficiently (cached if possible)
            $totalUsers = Cache::remember('total_users_count', 300, function() {
                return DB::table('users')->count();
            });
            
            // Optimized pagination with cursor-based approach for large datasets
            $perPage = min($request->get('per_page', 50), 100); // Increased default to 50
            
            // Use simple query with only essential fields for better performance
            $users = DB::table('users')
                ->select([
                    'id',
                    'name', 
                    'first_name',
                    'last_name',
                    'email',
                    'gender',
                    'country',
                    'industry',
                    'status',
                    'created_at'
                ])
                ->orderBy('id', 'desc') // Use id for better performance than created_at
                ->paginate($perPage);
            
            // Add query parameters to pagination links
            $users->appends($request->query());

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            return view('users.index', [
                'users' => $users,
                'totalUsers' => $totalUsers,
                'executionTime' => $executionTime
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in viewUsers: ' . $e->getMessage());
            return redirect()->route('bulk-users.index')
                ->with('error', 'Error loading users: ' . $e->getMessage());
        }
    }
}
