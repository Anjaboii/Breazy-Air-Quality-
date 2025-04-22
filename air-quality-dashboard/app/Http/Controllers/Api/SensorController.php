<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\AqiReading;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SensorController extends Controller
{
    public function index()
    {
        return Sensor::with('latestReading')
            ->where('is_active', true)
            ->get();
    }
    
    public function details(Sensor $sensor)
    {
        $latestReading = $sensor->latestReading;
        
        // Get readings from the last 24 hours
        $historicalReadings = AqiReading::where('sensor_id', $sensor->id)
            ->where('reading_timestamp', '>=', Carbon::now()->subDay())
            ->orderBy('reading_timestamp')
            ->get();
        
        return [
            'sensor' => $sensor,
            'latest_reading' => $latestReading,
            'historical_readings' => $historicalReadings
        ];
    }
}