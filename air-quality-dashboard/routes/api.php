<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\AqiLocationController;

Route::get('/aqi-locations', [DashboardController::class, 'getAqiLocations']);
Route::get('/sensors', [DashboardController::class, 'getSensors']);
Route::get('/sensors/{sensor}/readings', [DashboardController::class, 'getSensorReadings']);
Route::get('/aqi-locations', [AqiLocationController::class, 'index']);