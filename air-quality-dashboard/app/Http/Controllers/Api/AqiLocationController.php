<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AqiLocation;

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

}