<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'prevent.back' => \App\Http\Middleware\PreventBackAfterLogout::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        // Schedule reminder check to run every minute
        $schedule->command('reminders:send')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
        
        // Schedule notification check to run every minute
        $schedule->command('notifications:send-scheduled')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
        
        // Schedule completed markets check to run every minute
        $schedule->command('markets:check-completed')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
        
        // Schedule in-play market check to run every minute
        $schedule->command('markets:check-inplay')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
        
        // Schedule scorecard labels check to run every minute
        $schedule->command('scorecard:check-labels')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
