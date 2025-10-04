<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BulkUserController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// Bulk User Routes
Route::get('/bulk-users', [BulkUserController::class, 'index'])->name('bulk-users.index');
Route::post('/bulk-users', [BulkUserController::class, 'store'])->name('bulk-users.store');
Route::delete('/bulk-users', [BulkUserController::class, 'clear'])->name('bulk-users.clear');
Route::get('/bulk-users/resources', [BulkUserController::class, 'getResources'])->name('bulk-users.resources');

// Users View Routes
Route::get('/users', [BulkUserController::class, 'viewUsers'])->name('users.index');

// Database Test Route
Route::get('/database-test', [BulkUserController::class, 'testDatabase'])->name('database.test');

// CSRF Token Refresh Route
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');

// Test CSRF Route
Route::post('/test-csrf', function (Request $request) {
    return response()->json(['message' => 'CSRF token is valid', 'token' => $request->input('_token')]);
});
