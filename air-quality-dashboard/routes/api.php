<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\AqiLocationController;

// Removed the duplicate route for aqi-locations
Route::get('/sensors', [DashboardController::class, 'getSensors']);
Route::get('/sensors/{sensor}/readings', [DashboardController::class, 'getSensorReadings']);
Route::get('/aqi-locations', [AqiLocationController::class, 'index']);
// Remove this line since it creates a duplicate path with 'api/api/aqi-locations'
// Route::get('/api/aqi-locations', [AQILocationController::class, 'getLocations']);