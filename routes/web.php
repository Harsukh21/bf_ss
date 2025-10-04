<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BulkUserController;

Route::get('/', function () {
    return view('welcome');
});

// Bulk User Routes
Route::get('/bulk-users', [BulkUserController::class, 'index'])->name('bulk-users.index');
Route::post('/bulk-users', [BulkUserController::class, 'store'])->name('bulk-users.store');
Route::delete('/bulk-users', [BulkUserController::class, 'clear'])->name('bulk-users.clear');
Route::get('/bulk-users/resources', [BulkUserController::class, 'getResources'])->name('bulk-users.resources');
