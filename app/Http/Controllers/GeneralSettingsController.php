<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class GeneralSettingsController extends Controller
{
    public function index()
    {
        return view('general-settings.index');
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            return redirect()->route('general-settings.index')
                ->with('success', 'All caches cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->route('general-settings.index')
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    public function optimize()
    {
        $results = [];
        $errors = [];
        
        try {
            // Clear existing caches first
            try {
                Artisan::call('optimize:clear');
                $results[] = 'Cleared optimization cache';
            } catch (\Exception $e) {
                // If optimize:clear doesn't exist, manually clear
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
                Artisan::call('cache:clear');
                $results[] = 'Cleared all caches manually';
            }
            
            // Cache configuration
            try {
                Artisan::call('config:cache');
                $results[] = 'Configuration cached';
            } catch (\Exception $e) {
                $errors[] = 'config:cache failed: ' . $e->getMessage();
            }
            
            // Cache routes (skip if in development with closures)
            try {
                if (app()->environment('production')) {
                    Artisan::call('route:cache');
                    $results[] = 'Routes cached';
                } else {
                    // In development, just ensure routes are not cached
                    try {
                        Artisan::call('route:clear');
                    } catch (\Exception $e) {
                        // Ignore route:clear errors
                    }
                }
            } catch (\Exception $e) {
                $errors[] = 'route:cache failed: ' . $e->getMessage();
            }
            
            // Cache views
            try {
                Artisan::call('view:cache');
                $results[] = 'Views cached';
            } catch (\Exception $e) {
                $errors[] = 'view:cache failed: ' . $e->getMessage();
            }
            
            $message = 'Application optimized successfully. ' . implode(', ', $results);
            if (!empty($errors)) {
                $message .= ' (Some operations had warnings: ' . implode('; ', $errors) . ')';
            }
            
            return redirect()->route('general-settings.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('general-settings.index')
                ->with('error', 'Failed to optimize: ' . $e->getMessage());
        }
    }

    public function getInfo()
    {
        $info = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'app_name' => config('app.name'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'timezone' => config('app.timezone'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'db_connection' => config('database.default'),
        ];

        return response()->json($info);
    }
}

