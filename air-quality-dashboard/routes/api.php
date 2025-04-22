<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorController;

// Sensor data APIs (no auth required - public access)
Route::get('/sensors', [SensorController::class, 'index']);
Route::get('/sensors/{sensor}/details', [SensorController::class, 'details']);