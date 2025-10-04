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
        try {
            $userCount = DB::table('users')->count();
        } catch (\Exception $e) {
            $userCount = 0;
        }
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
                $faker = \Faker\Factory::create();
                $batchData[] = [
                    'name' => "User {$userNum}",
                    'first_name' => "User",
                    'last_name' => $userNum,
                    'email' => "user{$userNum}_{$timestamp}@example.com", // Add timestamp for uniqueness
                    'email_verified_at' => $now,
                    'password' => $hashedPassword,
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
                    'linkedin_url' => 'https://linkedin.com/in/user' . $userNum,
                    'twitter_handle' => '@user' . $userNum,
                    'status' => 'active',
                    'email_notifications' => true,
                    'sms_notifications' => $faker->boolean(),
                    'avatar' => null,
                    'notes' => 'Bulk inserted user #' . $userNum,
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
        $load = sys_getloadavg();
        $cpuCount = $this->getCpuCount();
        return round(($load[0] / $cpuCount) * 100, 1);
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
     * Display all users with filtering and pagination
     */
    public function viewUsers(Request $request)
    {
        $query = DB::table('users');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('first_name', 'ILIKE', "%{$search}%")
                  ->orWhere('last_name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('company', 'ILIKE', "%{$search}%")
                  ->orWhere('job_title', 'ILIKE', "%{$search}%")
                  ->orWhere('city', 'ILIKE', "%{$search}%")
                  ->orWhere('country', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->get('gender'));
        }

        if ($request->filled('industry')) {
            $query->where('industry', $request->get('industry'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('country')) {
            $query->where('country', $request->get('country'));
        }

        if ($request->filled('salary_min')) {
            $query->where('salary', '>=', $request->get('salary_min'));
        }

        if ($request->filled('salary_max')) {
            $query->where('salary', '<=', $request->get('salary_max'));
        }

        // Get filter options for dropdowns
        $genders = DB::table('users')->select('gender')->distinct()->whereNotNull('gender')->pluck('gender');
        $industries = DB::table('users')->select('industry')->distinct()->whereNotNull('industry')->pluck('industry');
        $countries = DB::table('users')->select('country')->distinct()->whereNotNull('country')->pluck('country');
        $statuses = ['active', 'inactive', 'suspended'];

        // Get statistics
        $totalUsers = DB::table('users')->count();
        $activeUsers = DB::table('users')->where('status', 'active')->count();
        $avgSalary = DB::table('users')->whereNotNull('salary')->avg('salary');
        $topCountries = DB::table('users')
            ->select('country', DB::raw('COUNT(*) as count'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Paginate results
        $perPage = $request->get('per_page', 20);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Convert timestamps to Carbon objects for proper formatting
        $users->getCollection()->transform(function ($user) {
            if ($user->created_at) {
                $user->created_at = \Carbon\Carbon::parse($user->created_at);
            }
            if ($user->updated_at) {
                $user->updated_at = \Carbon\Carbon::parse($user->updated_at);
            }
            if ($user->last_login_at) {
                $user->last_login_at = \Carbon\Carbon::parse($user->last_login_at);
            }
            return $user;
        });

        // Add request parameters to pagination links
        $users->appends($request->query());

        return view('users.index', compact(
            'users', 
            'genders', 
            'industries', 
            'countries', 
            'statuses',
            'totalUsers',
            'activeUsers',
            'avgSalary',
            'topCountries'
        ));
    }
}
