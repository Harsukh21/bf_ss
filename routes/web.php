<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (protected)
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
