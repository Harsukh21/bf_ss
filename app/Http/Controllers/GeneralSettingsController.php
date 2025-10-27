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
        try {
            Artisan::call('optimize:clear');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            
            return redirect()->route('general-settings.index')
                ->with('success', 'Application optimized successfully.');
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

