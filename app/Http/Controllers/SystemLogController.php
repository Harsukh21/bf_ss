<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $logPath = storage_path('logs');
        $logFiles = [];
        
        // Get all log files from storage/logs directory
        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                $logFiles[] = [
                    'name' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                ];
            }
            
            // Sort by modification time (newest first)
            usort($logFiles, function ($a, $b) {
                return $b['modified'] - $a['modified'];
            });
        }

        // Pagination
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedFiles = array_slice($logFiles, $offset, $perPage);
        
        // Create pagination data
        $total = count($logFiles);
        $lastPage = ceil($total / $perPage);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedFiles,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        return view('system-logs.index', compact('paginator'));
    }

    public function view(Request $request, $filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath)) {
            return redirect()->route('system-logs.index')->with('error', 'Log file not found.');
        }

        // Get file content with pagination
        $perPage = 100; // Number of lines per page
        $currentPage = $request->get('page', 1);
        
        $lines = file($logPath);
        $totalLines = count($lines);
        
        // Reverse array to show newest entries first
        $lines = array_reverse($lines);
        
        $offset = ($currentPage - 1) * $perPage;
        $paginatedLines = array_slice($lines, $offset, $perPage);
        
        // Create pagination data
        $lastPage = ceil($totalLines / $perPage);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedLines,
            $totalLines,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        return view('system-logs.view', compact('paginator', 'filename'));
    }

    public function download($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath)) {
            return redirect()->route('system-logs.index')->with('error', 'Log file not found.');
        }

        return response()->download($logPath);
    }

    public function clear($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath)) {
            return redirect()->route('system-logs.index')->with('error', 'Log file not found.');
        }

        try {
            // Clear/truncate the file instead of deleting it
            File::put($logPath, '');
            return redirect()->route('system-logs.index')->with('success', 'Log file "' . $filename . '" cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->route('system-logs.index')->with('error', 'Failed to clear log file: ' . $e->getMessage());
        }
    }

    public function clearAll()
    {
        $logPath = storage_path('logs');
        
        try {
            if (!File::exists($logPath)) {
                return redirect()->route('system-logs.index')->with('error', 'Logs directory not found.');
            }
            
            $files = File::files($logPath);
            $clearedCount = 0;
            
            foreach ($files as $file) {
                try {
                    // Clear/truncate the file instead of deleting it
                    File::put($file->getPathname(), '');
                    $clearedCount++;
                } catch (\Exception $e) {
                    // Continue with other files if one fails
                    \Log::error('Failed to clear log file: ' . $file->getFilename() . ' - ' . $e->getMessage());
                }
            }
            
            if ($clearedCount > 0) {
                return redirect()->route('system-logs.index')->with('success', 'All log files (' . $clearedCount . ') cleared successfully.');
            } else {
                return redirect()->route('system-logs.index')->with('error', 'No log files found to clear.');
            }
        } catch (\Exception $e) {
            return redirect()->route('system-logs.index')->with('error', 'Failed to clear log files: ' . $e->getMessage());
        }
    }

    public function refresh()
    {
        return redirect()->route('system-logs.index')->with('success', 'Log list refreshed.');
    }

    /**
     * Display database system logs with filters
     */
    public function databaseLogs(Request $request)
    {
        $query = DB::table('system_logs')
            ->leftJoin('users', 'system_logs.user_id', '=', 'users.id')
            ->select([
                'system_logs.id',
                'system_logs.user_id',
                'system_logs.action',
                'system_logs.description',
                'system_logs.exEventId',
                'system_logs.label_name',
                'system_logs.old_value',
                'system_logs.new_value',
                'system_logs.event_name',
                'system_logs.ip_address',
                'system_logs.user_agent',
                'system_logs.created_at',
                'users.name as user_name',
                'users.email as user_email',
            ])
            ->orderBy('system_logs.created_at', 'desc');

        // Apply filters
        if ($request->filled('action')) {
            $query->where('system_logs.action', $request->input('action'));
        }

        if ($request->filled('label_name')) {
            $query->where('system_logs.label_name', $request->input('label_name'));
        }

        if ($request->filled('user_id')) {
            $query->where('system_logs.user_id', $request->input('user_id'));
        }

        if ($request->filled('exEventId')) {
            $query->where('system_logs.exEventId', 'like', '%' . $request->input('exEventId') . '%');
        }

        if ($request->filled('date_from')) {
            try {
                $dateFrom = \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('date_from'))->startOfDay();
                $query->where('system_logs.created_at', '>=', $dateFrom);
            } catch (\Exception $e) {
                // Invalid date format, skip filter
            }
        }

        if ($request->filled('date_to')) {
            try {
                $dateTo = \Carbon\Carbon::createFromFormat('Y-m-d', $request->input('date_to'))->endOfDay();
                $query->where('system_logs.created_at', '<=', $dateTo);
            } catch (\Exception $e) {
                // Invalid date format, skip filter
            }
        }

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function($q) use ($search) {
                $q->where('system_logs.description', 'like', $search)
                  ->orWhere('system_logs.event_name', 'like', $search)
                  ->orWhere('users.name', 'like', $search)
                  ->orWhere('users.email', 'like', $search);
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        // Get filter options
        $actions = DB::table('system_logs')
            ->select('action')
            ->distinct()
            ->whereNotNull('action')
            ->orderBy('action')
            ->pluck('action');

        $labelNames = DB::table('system_logs')
            ->select('label_name')
            ->distinct()
            ->whereNotNull('label_name')
            ->orderBy('label_name')
            ->pluck('label_name');

        $users = DB::table('users')
            ->select('id', 'name', 'email')
            ->whereIn('id', function($query) {
                $query->select('user_id')
                    ->from('system_logs')
                    ->whereNotNull('user_id')
                    ->distinct();
            })
            ->orderBy('name')
            ->get();

        return view('system-logs.database', compact('logs', 'actions', 'labelNames', 'users'));
    }
}