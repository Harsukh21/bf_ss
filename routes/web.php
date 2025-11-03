<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\MarketRateController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GeneralSettingsController;

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth', 'prevent.back'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/search', [UserController::class, 'search'])->name('search');
        Route::patch('/{user}/update-status', [UserController::class, 'updateStatus'])->name('update-status');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
    
    // Events
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/bulk-update', [EventController::class, 'bulkUpdate'])->name('events.bulk-update');
    Route::get('/events/stats', [EventController::class, 'getStats'])->name('events.stats');
    Route::get('/events/search', [EventController::class, 'search'])->name('events.search');

    // Markets
    Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');
    Route::get('/markets/export', [MarketController::class, 'export'])->name('markets.export');
    Route::get('/markets/{id}', [MarketController::class, 'show'])->name('markets.show');

    // Market Rates Management (Dynamic tables)
    Route::prefix('market-rates')->name('market-rates.')->group(function () {
        Route::get('/', [MarketRateController::class, 'index'])->name('index');
        Route::get('/{id}', [MarketRateController::class, 'show'])->name('show');
        Route::get('/search', [MarketRateController::class, 'search'])->name('search');
        Route::get('/count', [MarketRateController::class, 'getCount'])->name('count');
        Route::get('/export/csv', [MarketRateController::class, 'export'])->name('export');
    });

    // System Logs
    Route::prefix('system-logs')->name('system-logs.')->group(function () {
        Route::get('/', [SystemLogController::class, 'index'])->name('index');
        Route::get('/view/{filename}', [SystemLogController::class, 'view'])->name('view');
        Route::get('/download/{filename}', [SystemLogController::class, 'download'])->name('download');
        Route::post('/clear/{filename}', [SystemLogController::class, 'clear'])->name('clear');
        Route::delete('/clear-all', [SystemLogController::class, 'clearAll'])->name('clear-all');
        Route::post('/refresh', [SystemLogController::class, 'refresh'])->name('refresh');
    });

    // Performance
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [PerformanceController::class, 'index'])->name('index');
        Route::post('/refresh', [PerformanceController::class, 'refresh'])->name('refresh');
        Route::get('/live-data', [PerformanceController::class, 'getLiveData'])->name('live-data');
    });

    // General Settings
    Route::prefix('general-settings')->name('general-settings.')->group(function () {
        Route::get('/', [GeneralSettingsController::class, 'index'])->name('index');
        Route::post('/clear-cache', [GeneralSettingsController::class, 'clearCache'])->name('clear-cache');
        Route::post('/optimize', [GeneralSettingsController::class, 'optimize'])->name('optimize');
        Route::get('/info', [GeneralSettingsController::class, 'getInfo'])->name('info');
    });
    
    // Profile & Settings
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/update', [ProfileController::class, 'updateProfile'])->name('update');
        Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password');
        
        // 2FA routes
        Route::get('/two-factor', [ProfileController::class, 'twoFactorIndex'])->name('two-factor');
        Route::post('/two-factor/enable', [ProfileController::class, 'enableTwoFactor'])->name('two-factor.enable');
        Route::post('/two-factor/disable', [ProfileController::class, 'disableTwoFactor'])->name('two-factor.disable');
        
        // Security routes
        Route::get('/security', [ProfileController::class, 'securityIndex'])->name('security');
        Route::post('/logout-all-devices', [ProfileController::class, 'logoutAllDevices'])->name('logout-all-devices');
        Route::delete('/terminate-session/{sessionId}', [ProfileController::class, 'terminateSession'])->name('terminate-session');
    });
});
