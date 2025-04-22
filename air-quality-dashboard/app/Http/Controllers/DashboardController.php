<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function contact()
    {
        return view('contact');
    }

    public function getSensors()
    {
        $sensors = Sensor::where('is_active', true)->get();
        
        // If no sensors in DB, fetch from WAQI
        if ($sensors->isEmpty()) {
            return $this->fetchFromWAQI();
        }
        
        return response()->json($sensors);
    }

    public function getReadings(Sensor $sensor)
    {
        $readings = $sensor->readings()
            ->orderBy('timestamp', 'desc')
            ->limit(24)
            ->get();
            
        return response()->json($readings);
    }

    private function fetchFromWAQI()
    {
        $response = Http::get('https://api.waqi.info/map/bounds', [
            'latlng' => '6.7,79.7,7.0,80.0', // Colombo area bounds
            'token' => env('WAQI_API_KEY')
        ]);
        
        $stations = $response->json()['data'];
        
        $sensors = [];
        foreach ($stations as $station) {
            $sensors[] = [
                'id' => $station['uid'],
                'name' => $station['station']['name'],
                'latitude' => $station['lat'],
                'longitude' => $station['lon'],
                'aqi' => $station['aqi'],
                'is_active' => true
            ];
        }
        
        return response()->json($sensors);
    }
}