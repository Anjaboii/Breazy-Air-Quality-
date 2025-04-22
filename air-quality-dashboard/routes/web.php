<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/contact', [DashboardController::class, 'contact'])->name('contact');

// Authentication
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/sensors', [AdminController::class, 'sensors'])->name('admin.sensors');
    Route::get('/locations', [AdminController::class, 'locations'])->name('admin.locations');
    
    // API endpoints for admin
    Route::post('/sensors', [AdminController::class, 'storeSensor']);
    Route::put('/sensors/{sensor}', [AdminController::class, 'updateSensor']);
    Route::delete('/sensors/{sensor}', [AdminController::class, 'deleteSensor']);
});

// API routes for frontend
Route::get('/api/sensors', [DashboardController::class, 'getSensors']);
Route::get('/api/readings/{sensor}', [DashboardController::class, 'getReadings']);