<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AqiController;

Route::get('/', function () {
    return view('dashboard');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');



Route::get('/contact', function () {
    return view('contactus');
})->name('contactus');

Route::get('/map-api', function () {
    return view('map-api');
})->name('map.api');

// API endpoints
Route::get('/api/aqi/locations', [AqiController::class, 'getLocations'])->name('aqi.locations');
Route::get('/api/aqi/sensors', [AqiController::class, 'getSensors'])->name('aqi.sensors');

// Authentication routes

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
