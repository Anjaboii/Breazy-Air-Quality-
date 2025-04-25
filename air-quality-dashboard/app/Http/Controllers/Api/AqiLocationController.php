<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AqiLocation;
use App\Models\AqiHistory;

class AqiLocationController extends Controller
{
    public function index()
    {
        return response()->json(AqiLocation::all());
    }

    public function getLocations()
{
    $locations = AQILocation::all(); // Or with any filtering you need
    return response()->json($locations);
}

public function show()
    {
        // Fetch all AQI data
        $aqiHistory = AqiHistory::with('location')->orderBy('date', 'desc')->get();

        return view('aqi.index', compact('aqiHistory'));
    }

}