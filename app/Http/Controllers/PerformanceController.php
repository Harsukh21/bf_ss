<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    public function index()
    {
        $systemInfo = $this->getSystemInfo();
        $databaseInfo = $this->getDatabaseInfo();
        $phpInfo = $this->getPhpInfo();
        $memoryInfo = $this->getMemoryInfo();
        $diskInfo = $this->getDiskInfo();
        $networkInfo = $this->getNetworkInfo();
        $processInfo = $this->getProcessInfo();

        return view('performance.index', compact(
            'systemInfo',
            'databaseInfo', 
            'phpInfo',
            'memoryInfo',
            'diskInfo',
            'networkInfo',
            'processInfo'
        ));
    }

    public function refresh()
    {
        return redirect()->route('performance.index')->with('success', 'Performance data refreshed.');
    }

    public function getLiveData()
    {
        $systemInfo = $this->getSystemInfo();
        $memoryInfo = $this->getMemoryInfo();
        
        // Calculate CPU usage percentage from load average
        $cpuUsage = 0;
        if (is_array($systemInfo['load_average']) && $systemInfo['cpu_count'] !== 'N/A') {
            $avgLoad = $systemInfo['load_average'][0]; // 1-minute load average
            $cpuCount = (int)$systemInfo['cpu_count'];
            $cpuUsage = min(($avgLoad / $cpuCount) * 100, 100);
        }
        
        return response()->json([
            'cpu' => [
                'load_average' => $systemInfo['load_average'],
                'cpu_count' => $systemInfo['cpu_count'],
                'usage_percentage' => round($cpuUsage, 2)
            ],
            'memory' => [
                'current_usage' => $memoryInfo['current_usage'],
                'peak_usage' => $memoryInfo['peak_usage'],
                'usage_percentage' => $memoryInfo['usage_percentage'],
                'system_total' => $memoryInfo['system_memory']['total'] ?? 'N/A',
                'system_used' => $memoryInfo['system_memory']['used'] ?? 'N/A',
                'system_available' => $memoryInfo['system_memory']['available'] ?? 'N/A',
                'system_usage_percentage' => $memoryInfo['system_memory']['usage_percentage'] ?? 0
            ],
            'timestamp' => now()->format('H:i:s')
        ]);
    }

    private function getSystemInfo()
    {
        $uptime = $this->getUptime();
        $loadAverage = sys_getloadavg();
        
        return [
            'os' => PHP_OS,
            'hostname' => gethostname(),
            'uptime' => $uptime,
            'load_average' => $loadAverage,
            'cpu_count' => $this->getCpuCount(),
            'timezone' => date_default_timezone_get(),
        ];
    }

    private function getDatabaseInfo()
    {
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            $driver = $connection->getDriverName();
            
            // Get version based on database type
            $version = $pdo->query('SELECT version()')->fetchColumn();
            
            $info = [
                'driver' => $driver,
                'version' => $version,
                'database' => $connection->getDatabaseName(),
                'host' => $connection->getConfig('host'),
                'port' => $connection->getConfig('port'),
                'charset' => $connection->getConfig('charset'),
                'connections' => 'N/A',
                'running_queries' => 'N/A',
                'uptime' => 'N/A',
            ];
            
            // Get database-specific information
            if ($driver === 'mysql') {
                $status = $pdo->query('SHOW STATUS')->fetchAll(\PDO::FETCH_KEY_PAIR);
                $info['connections'] = $status['Threads_connected'] ?? 'N/A';
                $info['running_queries'] = $status['Threads_running'] ?? 'N/A';
                $info['uptime'] = $this->formatSeconds($status['Uptime'] ?? 0);
            } elseif ($driver === 'pgsql') {
                // PostgreSQL specific queries
                try {
                    $connections = $pdo->query('SELECT count(*) FROM pg_stat_activity')->fetchColumn();
                    $info['connections'] = $connections;
                    
                    $running = $pdo->query('SELECT count(*) FROM pg_stat_activity WHERE state = \'active\'')->fetchColumn();
                    $info['running_queries'] = $running;
                    
                    $uptime = $pdo->query('SELECT EXTRACT(EPOCH FROM (now() - pg_postmaster_start_time()))')->fetchColumn();
                    $info['uptime'] = $this->formatSeconds((int)$uptime);
                } catch (\Exception $e) {
                    // If specific queries fail, keep default values
                }
            } elseif ($driver === 'sqlite') {
                // SQLite specific information
                $info['connections'] = 1; // SQLite is single connection
                $info['running_queries'] = 0;
                $info['uptime'] = 'N/A';
            }
            
            return $info;
        } catch (\Exception $e) {
            return [
                'error' => 'Unable to connect to database: ' . $e->getMessage()
            ];
        }
    }

    private function getPhpInfo()
    {
        return [
            'version' => PHP_VERSION,
            'sapi' => php_sapi_name(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'opcache_enabled' => ini_get('opcache.enable'),
            'opcache_memory_consumption' => ini_get('opcache.memory_consumption'),
            'extensions' => implode(', ', get_loaded_extensions()),
        ];
    }

    private function getMemoryInfo()
    {
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = $this->convertToBytes(ini_get('memory_limit'));
        
        // Calculate usage percentage, handle unlimited memory (-1)
        $usagePercentage = 0;
        if ($memoryLimit > 0) {
            $usagePercentage = round(($memoryUsage / $memoryLimit) * 100, 2);
        } elseif ($memoryLimit == -1) {
            // For unlimited memory, calculate percentage based on system memory
            $systemMemory = $this->getSystemMemoryInfo();
            if (isset($systemMemory['total']) && $systemMemory['total'] !== 'N/A') {
                $systemTotalBytes = $this->convertBytesToInt($systemMemory['total']);
                if ($systemTotalBytes > 0) {
                    $usagePercentage = round(($memoryUsage / $systemTotalBytes) * 100, 2);
                }
            } else {
                // Fallback: use a reasonable percentage based on typical usage
                $usagePercentage = round(($memoryUsage / (1024 * 1024 * 1024)) * 100, 2); // 1GB base
            }
        }
        
        return [
            'current_usage' => $this->formatBytes($memoryUsage),
            'peak_usage' => $this->formatBytes($memoryPeak),
            'limit' => $memoryLimit == -1 ? 'Unlimited' : $this->formatBytes($memoryLimit),
            'usage_percentage' => $usagePercentage,
            'system_memory' => $this->getSystemMemoryInfo(),
        ];
    }

    private function getDiskInfo()
    {
        $totalBytes = disk_total_space('/');
        $freeBytes = disk_free_space('/');
        $usedBytes = $totalBytes - $freeBytes;
        
        return [
            'total' => $this->formatBytes($totalBytes),
            'used' => $this->formatBytes($usedBytes),
            'free' => $this->formatBytes($freeBytes),
            'usage_percentage' => round(($usedBytes / $totalBytes) * 100, 2),
        ];
    }

    private function getNetworkInfo()
    {
        // Basic network info (this would need more sophisticated methods for detailed info)
        return [
            'hostname' => gethostname(),
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'N/A',
            'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
            'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'N/A',
        ];
    }

    private function getProcessInfo()
    {
        return [
            'pid' => getmypid(),
            'user' => get_current_user(),
            'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
        ];
    }

    private function getUptime()
    {
        if (function_exists('sys_getloadavg')) {
            $uptime = @file_get_contents('/proc/uptime');
            if ($uptime !== false) {
                $uptime = explode(' ', $uptime);
                return $this->formatSeconds((int)$uptime[0]);
            }
        }
        return 'N/A';
    }

    private function getCpuCount()
    {
        if (function_exists('sys_getloadavg')) {
            $cpuinfo = @file_get_contents('/proc/cpuinfo');
            if ($cpuinfo !== false) {
                return substr_count($cpuinfo, 'processor');
            }
        }
        return 'N/A';
    }

    private function getSystemMemoryInfo()
    {
        $meminfo = @file_get_contents('/proc/meminfo');
        if ($meminfo !== false) {
            $meminfo = explode("\n", $meminfo);
            $total = 0;
            $available = 0;
            
            foreach ($meminfo as $line) {
                if (strpos($line, 'MemTotal:') === 0) {
                    $total = (int)filter_var($line, FILTER_SANITIZE_NUMBER_INT) * 1024;
                } elseif (strpos($line, 'MemAvailable:') === 0) {
                    $available = (int)filter_var($line, FILTER_SANITIZE_NUMBER_INT) * 1024;
                }
            }
            
            return [
                'total' => $this->formatBytes($total),
                'available' => $this->formatBytes($available),
                'used' => $this->formatBytes($total - $available),
                'usage_percentage' => $total > 0 ? round((($total - $available) / $total) * 100, 2) : 0,
            ];
        }
        
        return ['total' => 'N/A', 'available' => 'N/A', 'used' => 'N/A', 'usage_percentage' => 0];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function convertToBytes($value)
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int)$value;
        
        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }

    private function convertBytesToInt($formattedBytes)
    {
        // Convert formatted bytes like "11.46 GB" back to integer bytes
        if (preg_match('/(\d+\.?\d*)\s*([KMGTP]?B)/i', $formattedBytes, $matches)) {
            $value = (float)$matches[1];
            $unit = strtoupper($matches[2]);
            
            switch ($unit) {
                case 'TB':
                    $value *= 1024;
                case 'GB':
                    $value *= 1024;
                case 'MB':
                    $value *= 1024;
                case 'KB':
                    $value *= 1024;
            }
            
            return (int)$value;
        }
        
        return 0;
    }

    private function formatSeconds($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        if ($days > 0) {
            return sprintf('%d days, %d hours, %d minutes', $days, $hours, $minutes);
        } elseif ($hours > 0) {
            return sprintf('%d hours, %d minutes', $hours, $minutes);
        } elseif ($minutes > 0) {
            return sprintf('%d minutes, %d seconds', $minutes, $secs);
        } else {
            return sprintf('%d seconds', $secs);
        }
    }
}