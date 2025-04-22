<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $sensors = Sensor::count();
        $activeSensors = Sensor::where('is_active', true)->count();
        
        return view('admin.dashboard', compact('sensors', 'activeSensors'));
    }

    public function sensors()
    {
        $sensors = Sensor::all();
        return view('admin.sensors', compact('sensors'));
    }

    public function locations()
    {
        $sensors = Sensor::all();
        return view('admin.locations', compact('sensors'));
    }

    public function storeSensor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        $sensor = Sensor::create($validated);
        
        return response()->json($sensor);
    }

    public function updateSensor(Request $request, Sensor $sensor)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'is_active' => 'sometimes|boolean',
        ]);
        
        $sensor->update($validated);
        
        return response()->json($sensor);
    }

    public function deleteSensor(Sensor $sensor)
    {
        $sensor->delete();
        return response()->json(['message' => 'Sensor deleted']);
    }
}