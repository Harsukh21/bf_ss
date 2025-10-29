<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
            file_put_contents($logPath, '');
            return redirect()->route('system-logs.view', $filename)->with('success', 'Log file cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->route('system-logs.view', $filename)->with('error', 'Failed to clear log file: ' . $e->getMessage());
        }
    }

    public function clearAll()
    {
        $logPath = storage_path('logs');
        
        try {
            $files = File::files($logPath);
            foreach ($files as $file) {
                // Clear/truncate the file instead of deleting it
                file_put_contents($file->getPathname(), '');
            }
            return redirect()->route('system-logs.index')->with('success', 'All log files cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->route('system-logs.index')->with('error', 'Failed to clear log files: ' . $e->getMessage());
        }
    }

    public function refresh()
    {
        return redirect()->route('system-logs.index')->with('success', 'Log list refreshed.');
    }
}