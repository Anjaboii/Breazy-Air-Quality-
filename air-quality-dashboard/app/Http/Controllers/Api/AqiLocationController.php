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
}