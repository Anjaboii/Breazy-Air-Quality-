<?php

namespace App\Http\Controllers;

use App\Services\AirQualityService;
use Illuminate\Http\Request;

class apicontroller extends apicontroller
{
    protected $airQualityService;

    public function __construct(AirQualityService $airQualityService)
    {
        $this->airQualityService = $airQualityService;
    }

    public function dashboard()
    {
        // Get data for default cities
        $colomboData = $this->airQualityService->getCityData('colombo');
        
        return view('air-quality.dashboard', [
            'colomboData' => $colomboData
        ]);
    }

    public function getStationData(Request $request)
    {
        $lat = $request->input('lat', 6.9271);
        $lng = $request->input('lng', 79.8612);
        
        $data = $this->airQualityService->getNearbyStations($lat, $lng);
        
        return response()->json($data);
    }
}