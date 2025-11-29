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
use App\Http\Controllers\RoleController;
use App\Http\Controllers\GeneralSettingsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ScriptController;

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
        Route::patch('/{user}/update-roles', [UserController::class, 'updateRoles'])->name('update-roles');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Roles & Permissions Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    });
    
    // Events
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/all', [EventController::class, 'all'])->name('events.all');
    Route::get('/events/export', [EventController::class, 'export'])->name('events.export');
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/bulk-update', [EventController::class, 'bulkUpdate'])->name('events.bulk-update');
    Route::post('/events/{event}/update-market-time', [EventController::class, 'updateMarketTime'])->name('events.update-market-time');
    Route::get('/events/stats', [EventController::class, 'getStats'])->name('events.stats');
    Route::get('/events/search', [EventController::class, 'search'])->name('events.search');

    // Markets
    Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');
    Route::get('/markets/all', [MarketController::class, 'all'])->name('markets.all');
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

    // Scorecard - Protected by permissions
    Route::prefix('scorecard')->name('scorecard.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ScorecardController::class, 'index'])
            ->middleware('permission:view-scorecard')
            ->name('index');
        Route::get('/events/{exEventId}/markets', [\App\Http\Controllers\ScorecardController::class, 'getEventMarkets'])
            ->middleware('permission:view-event-markets')
            ->name('events.markets');
        Route::post('/events/{exEventId}/update', [\App\Http\Controllers\ScorecardController::class, 'updateEvent'])
            ->middleware('permission:update-scorecard-events')
            ->name('events.update');
        Route::post('/events/{exEventId}/update-labels', [\App\Http\Controllers\ScorecardController::class, 'updateLabels'])
            ->middleware('permission:update-scorecard-labels')
            ->name('events.update-labels');
        Route::post('/events/{exEventId}/update-sc-type', [\App\Http\Controllers\ScorecardController::class, 'updateScType'])
            ->middleware('permission:update-scorecard-labels')
            ->name('events.update-sc-type');
    });

    // Risk Team
    Route::prefix('risk')->name('risk.')->group(function () {
        // Betlist Check - Keep same route names but use /betlist-check URL path
        Route::prefix('betlist-check')->group(function () {
        Route::get('/', [\App\Http\Controllers\RiskController::class, 'index'])->name('index');
        Route::get('/pending', [\App\Http\Controllers\RiskController::class, 'pending'])->name('pending');
        Route::get('/done', [\App\Http\Controllers\RiskController::class, 'done'])->name('done');
        Route::post('/markets/{market}/labels', [\App\Http\Controllers\RiskController::class, 'updateLabels'])->name('markets.labels');
        Route::post('/markets/{market}/done', [\App\Http\Controllers\RiskController::class, 'markDone'])->name('markets.done');
        });
        
        // Vol. Base Markets (placeholder for future functionality)
        Route::prefix('vol-base-markets')->name('vol-base-markets.')->group(function () {
            Route::get('/', [\App\Http\Controllers\RiskController::class, 'volBaseMarkets'])->name('index');
        });
        
        // Redirect old /risk route to betlist-check for backward compatibility
        Route::get('/', function () {
            return redirect()->route('risk.index');
        });
    });

    // Notifications - Protected by permissions
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])
            ->middleware('permission:view-notifications')
            ->name('index');
        Route::get('/create', [\App\Http\Controllers\NotificationController::class, 'create'])
            ->middleware('permission:create-notifications')
            ->name('create');
        Route::post('/', [\App\Http\Controllers\NotificationController::class, 'store'])
            ->middleware('permission:create-notifications')
            ->name('store');
        Route::get('/pending', [\App\Http\Controllers\NotificationController::class, 'getPendingNotifications'])
            ->middleware('permission:view-pending-notifications')
            ->name('pending');
        Route::get('/push/pending', [\App\Http\Controllers\NotificationController::class, 'getPushNotifications'])
            ->middleware('permission:manage-push-notifications')
            ->name('push.pending');
        Route::post('/push/{id}/mark-delivered', [\App\Http\Controllers\NotificationController::class, 'markPushDelivered'])
            ->middleware('permission:manage-push-notifications')
            ->name('push.mark-delivered');
        Route::get('/{id}', [\App\Http\Controllers\NotificationController::class, 'show'])
            ->middleware('permission:view-notification-details')
            ->name('show');
        Route::post('/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])
            ->middleware('permission:mark-notifications-as-read')
            ->name('mark-read');
        Route::get('/{id}/edit', [\App\Http\Controllers\NotificationController::class, 'edit'])
            ->middleware('permission:edit-notifications')
            ->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\NotificationController::class, 'update'])
            ->middleware('permission:edit-notifications')
            ->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])
            ->middleware('permission:delete-notifications')
            ->name('destroy');
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

    // Settings Management - Protected by permissions
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])
            ->middleware('permission:view-settings')
            ->name('index');
        Route::get('/create', [SettingsController::class, 'create'])
            ->middleware('permission:create-settings')
            ->name('create');
        Route::post('/', [SettingsController::class, 'store'])
            ->middleware('permission:create-settings')
            ->name('store');
        Route::get('/{setting}', [SettingsController::class, 'show'])
            ->middleware('permission:view-settings')
            ->name('show');
        Route::get('/{setting}/edit', [SettingsController::class, 'edit'])
            ->middleware('permission:edit-settings')
            ->name('edit');
        Route::put('/{setting}', [SettingsController::class, 'update'])
            ->middleware('permission:edit-settings')
            ->name('update');
        Route::delete('/{setting}', [SettingsController::class, 'destroy'])
            ->middleware('permission:delete-settings')
            ->name('destroy');
    });

    // Testing Routes - Protected by permissions
    Route::prefix('testing')->name('testing.')->group(function () {
        Route::prefix('telegram')->name('telegram.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Testing\TelegramTestController::class, 'index'])
                ->middleware('permission:access-testing-module')
                ->name('index');
            Route::post('/send', [\App\Http\Controllers\Testing\TelegramTestController::class, 'sendTestMessage'])
                ->middleware('permission:send-telegram-test-messages')
                ->name('send');
        });
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

Route::get('/script/get-view', [ScriptController::class, 'index']);
Route::post('/script/store-page', [ScriptController::class, 'store'])->name('runscript');