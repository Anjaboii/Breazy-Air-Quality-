<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SensorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AqiLocationController;
use App\Http\Controllers\Web\AqiLocationWebController;

/*
|---------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes

Route::prefix('aqi')->group(function() {
    Route::get('/locations/{location}', [AqiLocationWebController::class, 'show'])
         ->name('aqi.locations.show');
        });


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/contact', [DashboardController::class, 'contact'])->name('contact');

// Public AQI Routes (This should be accessible without login)
Route::get('/aqi-locations', [AqiLocationWebController::class, 'index'])->name('aqi.locations');
Route::get('/aqi-location/{id}', [AqiLocationWebController::class, 'show'])->name('aqi.location.show');
Route::get('/aqi-history', [AqiLocationWebController::class, 'show'])->name('aqi.history');  // Move this route outside of admin middleware

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
    // Add this line to access AQI locations from web routes
    Route::get('/aqi-locations', [AqiLocationController::class, 'index']);
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

    // Fixed duplicate route by removing /admin prefix (it's already in the group)
    Route::get('/locations', [AdminController::class, 'locations'])->name('admin.locations');
    Route::post('/aqi_locations', [AdminController::class, 'storeLocation'])->name('admin.aqi_locations.store');
   
    // Toggle sensor active/inactive
    Route::patch('/sensors/{sensor}/toggle', [AdminController::class, 'toggleSensor'])->name('admin.sensors.toggle');
    
    // Sensor CRUD Operations
    Route::put('/sensors/{sensor}', [SensorController::class, 'update'])->name('sensors.update');
    Route::delete('/sensors/{sensor}', [SensorController::class, 'destroy'])->name('sensors.destroy');

    
});



    
