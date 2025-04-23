<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SensorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AqiLocationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/contact', [DashboardController::class, 'contact'])->name('contact');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/admin/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// API Data Routes (Public)
Route::prefix('api')->group(function () {
    Route::get('/sensors', [DashboardController::class, 'getSensors']);
    Route::get('/readings/{sensor}', [DashboardController::class, 'getReadings']);
});

// Admin Protected Routes
Route::middleware('auth')->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Sensor Management
    Route::get('/sensors', [AdminController::class, 'sensors'])->name('admin.sensors');
    
    // AQI Locations Management
    Route::get('/locations', [AdminController::class, 'locations'])->name('admin.locations');
    
    // Sensor Store Route
    Route::post('/sensors', [SensorController::class, 'store'])->name('admin.sensors.store');

    Route::get('/admin/locations', [AdminController::class, 'locations'])->name('admin.locations');
    Route::post('/admin/aqi_locations', [AdminController::class, 'storeLocation'])->name('admin.aqi_locations.store');

    

    // Toggle sensor active/inactive
    Route::patch('/sensors/{sensor}/toggle', [AdminController::class, 'toggleSensor'])->name('admin.sensors.toggle');

    // Sensor CRUD Operations
    Route::put('/sensors/{sensor}', [SensorController::class, 'update'])->name('sensors.update');
    Route::delete('/sensors/{sensor}', [SensorController::class, 'destroy'])->name('sensors.destroy');
});


// Test Route (Temporary - can be removed after testing)
Route::get('/test-db-insert', function() {
    try {
        $sensor = \App\Models\Sensor::create([
            'name' => 'TEST_SENSOR_' . rand(100,999),
            'latitude' => 6.9271,
            'longitude' => 79.8612,
            'is_active' => true
        ]);
        
        return response()->json([
            'success' => true,
            'sensor' => $sensor,
            'message' => 'Direct DB insert successful!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'solution' => 'Check database connection and Sensor model'
        ], 500);
    }
});
